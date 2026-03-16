<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Http;

class AiGeneratorService
{
    protected string $apiKey;

    protected string $model;

    public function __construct()
    {
        $apiKey = (string) (config('services.gemini.key') ?? env('GEMINI_API_KEY'));
        if ($apiKey === '') {
            throw new Exception('Gemini API Key is not configured.');
        }

        $this->apiKey = $apiKey;
        $model = (string) (config('services.gemini.model') ?? env('GEMINI_MODEL', 'gemini-1.5-flash'));
        // Ensure model name doesn't have 'models/' prefix twice if concatenated in the URL
        // Ensure model name is clean for the URL interpolation
        $this->model = trim(preg_replace('/^models\//', '', $model));
    }

    /**
     * Generate quiz questions from text.
     *
     * @throws Exception
     */
    public function generateQuestions(string $text, int $questionCount, string $difficulty): array
    {
        $prompt = $this->buildPrompt($text, $questionCount, $difficulty);

        $response = $this->requestGenerateContent($this->model, $prompt);

        if (! $response->successful()) {
            $message = $response->json('error.message') ?: 'Gemini request failed.';

            if ($response->status() === 404) {
                $fallbackModel = $this->pickFallbackModel();
                if ($fallbackModel) {
                    $retry = $this->requestGenerateContent($fallbackModel, $prompt);
                    if ($retry->successful()) {
                        return $this->extractQuestionsFromResponse($retry);
                    }
                }

                $models = $this->listAvailableModels();
                if (count($models) === 0) {
                    throw new Exception('Gemini API Key tidak memiliki akses model text. Pastikan Generative Language API aktif dan billing/akses Gemini sudah tersedia.');
                }

                $modelNames = collect($models)->pluck('name')->take(10)->implode(', ');
                throw new Exception('Model Gemini tidak tersedia untuk API key ini. Model tersedia (contoh): '.$modelNames);
            }

            throw new Exception($message.' (HTTP '.$response->status().')');
        }

        return $this->extractQuestionsFromResponse($response);
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

    private function requestGenerateContent(string $model, string $prompt)
    {
        return Http::timeout(90)
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
                    'temperature' => 0.2,
                    'maxOutputTokens' => 2048,
                    'responseMimeType' => 'application/json',
                ],
            ]);
    }

    private function extractQuestionsFromResponse($response): array
    {
        $parts = $response->json('candidates.0.content.parts') ?? [];
        $responseBody = collect($parts)
            ->pluck('text')
            ->filter()
            ->implode("\n");

        if ($responseBody === '') {
            throw new Exception('AI returned an empty response.');
        }

        return $this->parseQuestionsFromText($responseBody);
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

        if (! array_key_exists('text', $first) || ! array_key_exists('options', $first)) {
            return false;
        }

        return is_array($first['options']);
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

    private function pickFallbackModel(): ?string
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
            ->values();

        $preferred = $candidates->first(function ($name) {
            return str_contains($name, 'gemini-2.5-flash');
        });

        if ($preferred) {
            return $preferred;
        }

        $preferred = $candidates->first(function ($name) {
            return str_contains($name, 'gemini-2.0-flash');
        });

        if ($preferred) {
            return $preferred;
        }

        $preferred = $candidates->first(function ($name) {
            return str_contains($name, 'gemini-1.5-flash');
        });

        return $preferred ?: $candidates->first();
    }

    /**
     * Build the prompt for Gemini.
     */
    private function buildPrompt(string $content, int $count, string $difficulty): string
    {
        return <<<PROMPT
You are a professional quiz generator. Based on the provided text, generate exactly {$count} multiple-choice questions.
Difficulty Level: {$difficulty}

The output MUST be a valid JSON array of objects. Each object must have:
- "text": The question string.
- "options": An array of at least 4 objects, each with:
    - "text": The option string.
    - "is_correct": A boolean (true for exactly one correct option, false otherwise).

Rules:
- Return ONLY a JSON array, no markdown, no code fences.
- Use double quotes for all JSON keys/strings.
- Do not use trailing commas.

Source Material:
{$content}

Return ONLY the JSON array. Do not include any explanation or markdown formatting outside the JSON block.
PROMPT;
    }
}
