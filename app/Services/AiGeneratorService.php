<?php

namespace App\Services;

use App\Models\Setting;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiGeneratorService
{
    protected string $apiKey;

    protected string $model;

    public function __construct()
    {
        $dbKey = Setting::get('gemini_api_key');
        $apiKey = (string) ($dbKey ?? config('services.gemini.key') ?? env('GEMINI_API_KEY'));
        
        if ($apiKey === '') {
            throw new Exception('Gemini API Key is not configured.');
        }

        $this->apiKey = $apiKey;
        
        $cachedModel = (string) cache('gemini_last_good_model');
        $dbModel = Setting::get('gemini_model');
        $model = (string) ($dbModel ?? config('services.gemini.model') ?? env('GEMINI_MODEL', $cachedModel ?: 'gemini-1.5-flash'));
        
        $this->model = trim(preg_replace('/^models\//', '', $model));
        
        Log::info("AiGeneratorService initialized with model: " . $this->model);
    }

    /**
     * Generate quiz questions from text.
     *
     * @throws Exception
     */
    public function generateQuestions(string $text, int $mcqCount, int $essayCount, string $difficulty, ?string $regenToken = null, string $language = 'id', ?array $fileData = null, bool $strictMode = false, array $tried = []): array
    {
        $prompt = $this->buildPrompt($text, $mcqCount, $essayCount, $difficulty, $regenToken, $language, ! empty($fileData), $strictMode);
        $maxOutputTokens = $this->recommendedMaxOutputTokens($mcqCount + $essayCount);

        $response = $this->requestGenerateContent($this->model, $prompt, $maxOutputTokens, $fileData);

        if (! $response->successful()) {
            $status = $response->status();
            $message = $response->json('error.message') ?: 'Gemini request failed.';
            
            // If model is unavailable, overloaded, or quota exceeded for this specific model
            if (in_array($status, [404, 429, 503, 500])) {
                $tried[] = $this->model;
                Log::warning("Gemini model {$this->model} failed with {$status}. Attempting self-healing rotation (Tried: ".implode(',', $tried).")...");
                
                $fallbackModel = $this->pickFallbackModel($tried);
                if ($fallbackModel && !in_array($fallbackModel, $tried)) {
                    $this->model = $fallbackModel;
                    // Persist for this session and globally for a while
                    cache(['gemini_last_good_model' => $fallbackModel], now()->addDay());
                    
                    return $this->generateQuestions($text, $mcqCount, $essayCount, $difficulty, $regenToken, $language, $fileData, $strictMode, $tried);
                }
            }

            throw new Exception($message.' (HTTP '.$status.')');
        }

        try {
            return $this->parseQuestionsFromText($this->extractTextFromResponse($response));
        } catch (Exception $e) {
            $finishReason = (string) ($response->json('candidates.0.finishReason') ?? '');
            if ($finishReason === 'MAX_TOKENS') {
                $retry = $this->requestGenerateContent($this->pickFallbackModel() ?: $this->model, $prompt, $this->recommendedMaxOutputTokens($mcqCount + $essayCount, true));
                if ($retry->successful()) {
                    return $this->parseQuestionsFromText($this->extractTextFromResponse($retry));
                }
            }

            throw $e;
        }
    }

    public function generateSingleExplanation(string $questionText, string $correctAnswer): string
    {
        $prompt = "Sebagai asisten kuis profesional dan teknis, berikan penjelasan mendalam (dalam bahasa Indonesia) mengapa jawaban '{$correctAnswer}' adalah yang paling tepat untuk pertanyaan: '{$questionText}'. 
        
        SYARAT PENJELASAN (Wajib):
        1. Gunakan gaya bahasa teknis dan formal.
        2. Jika soal melibatkan perhitungan angka, matematika, atau logika, WAJIB sertakan langkah-langkah perhitungan (step-by-step derivation) secara detail.
        3. Jelaskan konsep dasarnya agar peserta memahami 'mengapa' dan 'bagaimana' jawaban tersebut diperoleh.";
        
        return $this->generateInsight($prompt, 768);
    }

    public function generateInsight(string $prompt, int $maxOutputTokens = 512, array $tried = [], int $attempt = 1): string
    {
        try {
            $response = $this->requestGenerateTextContent($this->model, $prompt, $maxOutputTokens);

            if (! $response->successful()) {
                $status = $response->status();
                $message = $response->json('error.message') ?: 'Gemini request failed.';

                // Handle Rate Limiting (429) with simple sleep and retry
                if ($status === 429 && $attempt <= 3) {
                    $sleep = $attempt * 2;
                    Log::warning("Gemini Rate Limit (429). Sleeping {$sleep}s... (Attempt {$attempt}/3)");
                    sleep($sleep);
                    return $this->generateInsight($prompt, $maxOutputTokens, $tried, $attempt + 1);
                }

                if (in_array($status, [404, 429, 503, 500])) {
                    $tried[] = $this->model;
                    Log::warning("Gemini model {$this->model} failed (Insight) with {$status}. Rotating model (Tried: ".implode(',', $tried).")...");
                    $fallbackModel = $this->pickFallbackModel($tried);
                    if ($fallbackModel && !in_array($fallbackModel, $tried)) {
                        $this->model = $fallbackModel;
                        cache(['gemini_last_good_model' => $fallbackModel], now()->addDay());
                        return $this->generateInsight($prompt, $maxOutputTokens, $tried, 1); // Reset attempts for new model
                    }
                }

                throw new Exception($message.' (HTTP '.$status.')');
            }

            // Check for Safety Filter blocks
            $candidate = $response->json('candidates.0');
            if (isset($candidate['finishReason']) && ($candidate['finishReason'] === 'SAFETY' || $candidate['finishReason'] === 'OTHER')) {
                Log::warning("Gemini blocked content (Reason: {$candidate['finishReason']}). Retrying with softer prompt...");
                if ($attempt <= 2) {
                    $softerPrompt = "Please provide a safe, professional corporate explanation for: " . $prompt;
                    return $this->generateInsight($softerPrompt, $maxOutputTokens, $tried, $attempt + 1);
                }
                throw new Exception("Gemini refused to generate content due to safety filters.");
            }

            $finishReason = (string) ($candidate['finishReason'] ?? '');
            if ($finishReason === 'MAX_TOKENS' && $maxOutputTokens < 4096) {
                $retryTokens = min(4096, max($maxOutputTokens * 2, 768));
                $retry = $this->requestGenerateTextContent($this->model, $prompt, $retryTokens);
                if ($retry->successful()) {
                    return trim($this->extractTextFromResponse($retry));
                }
            }

            return trim($this->extractTextFromResponse($response));
        } catch (Exception $e) {
            if ($attempt <= 2 && !str_contains($e->getMessage(), 'refused')) {
                Log::error("Gemini Error: " . $e->getMessage() . ". Retrying once...");
                sleep(1);
                return $this->generateInsight($prompt, $maxOutputTokens, $tried, $attempt + 1);
            }
            throw $e;
        }
    }

    public function listAvailableModels(): array
    {
        $response = Http::timeout(30)
            ->withQueryParameters(['key' => $this->apiKey])
            ->acceptJson()
            ->get('https://generativelanguage.googleapis.com/v1beta/models');

        if (! $response->successful()) {
            return [];
        }

        $models = $response->json('models') ?? [];
        if (! is_array($models)) {
            return [];
        }

        return collect($models)
            ->map(function ($m) {
                return [
                    'name' => (string) ($m['name'] ?? ''),
                    'displayName' => (string) ($m['displayName'] ?? ''),
                    'supportedGenerationMethods' => $m['supportedGenerationMethods'] ?? [],
                    'inputTokenLimit' => $m['inputTokenLimit'] ?? null,
                    'outputTokenLimit' => $m['outputTokenLimit'] ?? null,
                ];
            })
            ->filter(function ($m) {
                return $m['name'] !== '';
            })
            ->values()
            ->all();
    }

    public function qualityCheck(array $questions): array
    {
        $result = [];

        foreach ($questions as $index => $question) {
            $issues = [];

            $questionText = (string) ($question['text'] ?? '');
            if (trim($questionText) === '') {
                $issues[] = 'Pertanyaan kosong.';
            } elseif (mb_strlen(trim($questionText)) < 10) {
                $issues[] = 'Pertanyaan terlalu pendek.';
            }

            $options = $question['options'] ?? null;
            if (! is_array($options)) {
                $issues[] = 'Opsi tidak valid.';
                $result[] = ['index' => $index, 'issues' => $issues];

                continue;
            }

            if (count($options) < 4) {
                $issues[] = 'Opsi kurang dari 4.';
            }

            $correctCount = 0;
            $seen = [];
            foreach ($options as $opt) {
                $optText = (string) ($opt['text'] ?? '');
                $normalized = mb_strtolower(trim($optText));

                if ($normalized === '') {
                    $issues[] = 'Ada opsi kosong.';
                } elseif (isset($seen[$normalized])) {
                    $issues[] = 'Ada opsi duplikat.';
                } else {
                    $seen[$normalized] = true;
                }

                $isCorrect = $opt['is_correct'] ?? false;
                if ($isCorrect === true || $isCorrect === 1 || $isCorrect === '1') {
                    $correctCount++;
                }

                if (mb_strlen(trim($optText)) > 220) {
                    $issues[] = 'Ada opsi terlalu panjang.';
                }
            }

            if ($correctCount !== 1) {
                $issues[] = 'Jawaban benar harus tepat 1.';
            }

            if (count($issues) > 0) {
                $result[] = ['index' => $index, 'issues' => array_values(array_unique($issues))];
            }
        }

        return $result;
    }

    private function requestGenerateContent(string $model, string $prompt, int $maxOutputTokens, ?array $fileData = null)
    {
        $parts = [];

        if ($fileData && isset($fileData['mime_type'], $fileData['data'])) {
            // Using snake_case for the raw REST API payload as per Google documentation
            $parts[] = [
                'inline_data' => [
                    'mime_type' => $fileData['mime_type'],
                    'data' => $fileData['data'],
                ],
            ];
            
            // Log that we are sending multimodal data (excluding the huge base64 string)
            Log::info("Sending multimodal PDF to Gemini. MIME: " . $fileData['mime_type']);
        }

        $parts[] = ['text' => $prompt];

        $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent";
        Log::debug("Gemini post URL: " . $url);

        return Http::timeout(120)
            ->withQueryParameters(['key' => $this->apiKey])
            ->acceptJson()
            ->post($url, [
                'contents' => [
                    [
                        'role' => 'user',
                        'parts' => $parts,
                    ],
                ],
                'generationConfig' => [
                    'temperature' => 0.2,
                    'maxOutputTokens' => $maxOutputTokens,
                    'responseMimeType' => 'application/json',
                ],
            ]);
    }

    private function requestGenerateTextContent(string $model, string $prompt, int $maxOutputTokens)
    {
        return Http::timeout(120)
            ->withQueryParameters(['key' => $this->apiKey])
            ->acceptJson()
            ->post("https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent", [
                'contents' => [
                    [
                        'role' => 'user',
                        'parts' => [
                            ['text' => $prompt],
                        ],
                    ],
                ],
                'generationConfig' => [
                    'temperature' => 0.3,
                    'maxOutputTokens' => $maxOutputTokens,
                ],
            ]);
    }

    private function extractTextFromResponse($response): string
    {
        $parts = $response->json('candidates.0.content.parts') ?? [];
        $responseBody = collect($parts)
            ->pluck('text')
            ->filter()
            ->implode("\n");

        if ($responseBody === '') {
            throw new Exception('AI returned an empty response.');
        }

        return $responseBody;
    }

    private function normalizeJson(string $json): string
    {
        $normalized = trim($json);
        $normalized = str_replace(["\u{201C}", "\u{201D}", "\u{201E}", "\u{00AB}", "\u{00BB}"], '"', $normalized);
        $normalized = str_replace(["\u{2018}", "\u{2019}", "\u{201A}"], "'", $normalized);
        $normalized = preg_replace('/,\s*([\]}])/m', '$1', $normalized) ?? $normalized;

        return $normalized;
    }

    private function parseQuestionsFromText(string $text): array
    {
        $normalized = $this->normalizeJson($text);

        $decoded = json_decode($normalized, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            $questions = $this->extractQuestionsFromDecoded($decoded);
            if (is_array($questions)) {
                return $questions;
            }
        }

        $arrayJson = $this->extractFirstJsonArray($normalized);
        if (is_string($arrayJson)) {
            $decodedArray = json_decode($this->normalizeJson($arrayJson), true);
            if (json_last_error() === JSON_ERROR_NONE && $this->looksLikeQuestionsArray($decodedArray)) {
                return $decodedArray;
            }
        }

        $objectJson = $this->extractFirstJsonObject($normalized);
        if (is_string($objectJson)) {
            $decodedObject = json_decode($this->normalizeJson($objectJson), true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $questions = $this->extractQuestionsFromDecoded($decodedObject);
                if (is_array($questions)) {
                    return $questions;
                }
            }
        }

        if ($this->looksLikeTruncatedJson($normalized)) {
            throw new Exception('Failed to decode AI response as JSON: Syntax error. Respons AI terpotong. Coba kurangi jumlah soal atau ringkas materi.');
        }

        $tail = mb_substr($normalized, 0, 600);
        throw new Exception('Failed to decode AI response as JSON: Syntax error. Cuplikan respons: '.trim($tail));
    }

    private function extractQuestionsFromDecoded($decoded): ?array
    {
        if ($this->looksLikeQuestionsArray($decoded)) {
            return $decoded;
        }

        if (is_array($decoded)) {
            foreach (['questions', 'data', 'items', 'result'] as $key) {
                if (isset($decoded[$key]) && $this->looksLikeQuestionsArray($decoded[$key])) {
                    return $decoded[$key];
                }
            }
        }

        return null;
    }

    private function looksLikeQuestionsArray($value): bool
    {
        if (! is_array($value) || count($value) === 0) {
            return false;
        }

        $first = $value[0] ?? null;
        if (! is_array($first)) {
            return false;
        }

        if (! array_key_exists('text', $first) || ! array_key_exists('type', $first)) {
            return false;
        }

        return true;
    }

    private function looksLikeTruncatedJson(string $text): bool
    {
        $trimmed = ltrim($text);
        if ($trimmed === '' || $trimmed[0] !== '[') {
            return false;
        }

        return strpos($trimmed, ']') === false;
    }

    private function extractFirstJsonArray(string $text): ?string
    {
        $start = strpos($text, '[');
        if ($start === false) {
            return null;
        }

        $depth = 0;
        $inString = false;
        $escape = false;
        $len = strlen($text);

        for ($i = $start; $i < $len; $i++) {
            $ch = $text[$i];

            if ($inString) {
                if ($escape) {
                    $escape = false;
                } elseif ($ch === '\\') {
                    $escape = true;
                } elseif ($ch === '"') {
                    $inString = false;
                }

                continue;
            }

            if ($ch === '"') {
                $inString = true;

                continue;
            }

            if ($ch === '[') {
                $depth++;
            } elseif ($ch === ']') {
                $depth--;
                if ($depth === 0) {
                    return substr($text, $start, $i - $start + 1);
                }
            }
        }

        return null;
    }

    private function extractFirstJsonObject(string $text): ?string
    {
        $start = strpos($text, '{');
        if ($start === false) {
            return null;
        }

        $depth = 0;
        $inString = false;
        $escape = false;
        $len = strlen($text);

        for ($i = $start; $i < $len; $i++) {
            $ch = $text[$i];

            if ($inString) {
                if ($escape) {
                    $escape = false;
                } elseif ($ch === '\\') {
                    $escape = true;
                } elseif ($ch === '"') {
                    $inString = false;
                }

                continue;
            }

            if ($ch === '"') {
                $inString = true;

                continue;
            }

            if ($ch === '{') {
                $depth++;
            } elseif ($ch === '}') {
                $depth--;
                if ($depth === 0) {
                    return substr($text, $start, $i - $start + 1);
                }
            }
        }

        return null;
    }

    private function pickFallbackModel(array $tried = []): ?string
    {
        $models = $this->listAvailableModels();

        $candidates = collect($models)
            ->filter(function ($m) {
                $methods = $m['supportedGenerationMethods'];
                return is_array($methods) && in_array('generateContent', $methods, true);
            })
            ->pluck('name')
            ->map(function ($name) {
                return trim(preg_replace('/^models\//', '', (string) $name));
            })
            ->filter(fn($name) => !in_array($name, $tried)) // Don't pick failing ones
            ->values();

        $preferredPatterns = [
            'gemini-1.5-flash',
            'gemini-2.0-flash-lite',
            'gemini-flash-latest',
            'gemini-2.0-flash',
            'gemini-2.5-flash-lite',
            'gemini-1.5-flash-8b',
        ];

        foreach ($preferredPatterns as $pattern) {
            $found = $candidates->first(fn($name) => str_contains($name, $pattern));
            if ($found) return $found;
        }

        return $candidates->first();
    }

    /**
     * Build the prompt for Gemini.
     */
    private function buildPrompt(string $content, int $mcqCount, int $essayCount, string $difficulty, ?string $regenToken = null, string $language = 'id', bool $hasAttachedFile = false, bool $strictMode = false): string
    {
        $content = $this->limitSourceMaterial($content);
        $regenLine = $regenToken ? "\nRegeneration token: {$regenToken}\n" : "\n";
        $langLine = $language === 'en' ? 'Output language: English' : 'Output language: Indonesian';

        $sourceInstruction = $hasAttachedFile
            ? "Materi pelajaran diberikan sepenuhnya melalui LAMPIRAN DOKUMEN (PDF) yang disertakan."
            : "Materi pelajaran diberikan melalui TEKS SUMBER di bawah.";

        $sourceMaterialBlock = "";
        if (!$hasAttachedFile || trim($content) !== "") {
            $sourceMaterialBlock = "Source Material (Text):\n{$content}\n";
        }

        $totalCount = $mcqCount + $essayCount;

        $strictInstruction = $strictMode ? "4. STRICT MODE: Use EXACT wording and phrases from the source material.\n" : "";

        return <<<PROMPT
You are a professional quiz generator for a high-stakes corporate training system. Your task is to generate exactly {$totalCount} questions, consisting of:
- {$mcqCount} Multiple-Choice (PG) questions.
- {$essayCount} Essay (Esai) questions.

STRICT GROUNDING RULES:
1. FACTUAL ACCURACY: Questions and content MUST be derived 100% and ONLY from the provided {$sourceInstruction}.
2. FORBIDDEN: DO NOT use general knowledge. Use the exact terminology from the document.
3. NO HALLUCINATION: If the document doesn't mention something, it doesn't exist for this quiz.
{$strictInstruction}
Context Details:
- Difficulty Level: {$difficulty}
- {$langLine}

The output MUST be a valid JSON array of objects. Each object must have:
- "type": String, either "mcq" or "essay".
- "text": The question string.
- "explanation": A detailed, technical reasoning (in Indonesian) for the correct/ideal answer.
- "options" (ONLY for type "mcq"): An array of at least 4 objects with "text" (string) and "is_correct" (boolean).
- "ideal_answer" (ONLY for type "essay"): A string containing the ideal/complete answer for grading.

Instruction:
- Return ONLY a JSON array, no markdown, no code fences.
- Use double quotes for all JSON keys/strings.
- Do not use trailing commas.
- Mix the order of MCQ and Essay questions naturally OR follow the order they appear in the source material.

{$sourceMaterialBlock}
{$regenLine}
PROMPT;
    }

    /**
     * Use AI to grade an essay answer against an ideal answer.
     */
    public function gradeEssayAnswer(string $question, string $idealAnswer, string $participantAnswer): array
    {
        // --- AI Anti-Prompt Injection Filter ---
        $injectionKeywords = ['abaikan', 'ignore', 'beri saya nilai', 'berikan saya nilai', 'lupakan', 'forget', 'prompt', 'instruction', 'instruksi', 'aturan', 'rules', 'bypass', 'system message'];
        $lowerAnswer = strtolower($participantAnswer);
        foreach ($injectionKeywords as $keyword) {
            if (str_contains($lowerAnswer, $keyword)) {
                Log::warning("Prompt Injection detected! Blocked answer: " . $participantAnswer);
                return [
                    'score' => 0, 
                    'feedback' => '⚠️ Sistem mendeteksi upaya manipulasi penilaian otomatis (Prompt Injection). Jawaban tidak valid.'
                ];
            }
        }

        $prompt = <<<PROMPT
Sebagai asisten penilaian kuis profesional, tugas Anda adalah menilai jawaban esai peserta berdasarkan Kunci Jawaban Ideal yang diberikan.

Pertanyaan: "{$question}"
Kunci Jawaban Ideal: "{$idealAnswer}"
Jawaban Peserta: "{$participantAnswer}"

KRITERIA PENILAIAN:
1. Skor diberikan dalam skala 0 sampai 5 (bilangan bulat).
2. Berikan skor 5 jika jawaban peserta mencakup poin-poin utama dalam kunci jawaban ideal dengan sangat baik.
3. Berikan skor 0 jika jawaban salah total atau tidak relevan.
4. Berikan alasan/umpan balik singkat (maksimal 2 kalimat) dalam Bahasa Indonesia mengapa skor tersebut diberikan.

Format output WAJIB JSON:
{
  "score": integer (0-5),
  "feedback": "string alasan penilaian"
}
PROMPT;

        try {
            $insight = $this->generateInsight($prompt, 256);
            $normalized = $this->normalizeJson($insight);
            $decoded = json_decode($normalized, true);
            
            if (json_last_error() === JSON_ERROR_NONE && isset($decoded['score'])) {
                return [
                    'score' => (int) $decoded['score'],
                    'feedback' => (string) ($decoded['feedback'] ?? 'Penilaian otomatis oleh AI.')
                ];
            }
        } catch (Exception $e) {
            Log::error("Essay Grading Error: " . $e->getMessage());
        }

        return ['score' => 0, 'feedback' => 'Gagal melakukan penilaian otomatis.'];
    }

    private function limitSourceMaterial(string $content): string
    {
        $maxChars = (int) env('AI_SOURCE_MAX_CHARS', 20000);
        $trimmed = trim($content);

        if ($maxChars <= 0 || mb_strlen($trimmed) <= $maxChars) {
            return $trimmed;
        }

        return mb_substr($trimmed, 0, $maxChars);
    }

    private function recommendedMaxOutputTokens(int $questionCount, bool $aggressive = false): int
    {
        $configured = (int) env('GEMINI_MAX_OUTPUT_TOKENS', 0);
        if ($configured > 0) {
            return $configured;
        }

        $base = $aggressive ? 8192 : 4096;
        $perQuestion = $aggressive ? 800 : 600;
        $tokens = $base + ($perQuestion * max(1, $questionCount));

        return min(16384, max(2048, $tokens));
    }
}
