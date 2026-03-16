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

        $jsonStart = strpos($responseBody, '[');
        $jsonEnd = strrpos($responseBody, ']') + 1;

        if ($jsonStart === false || $jsonEnd === false) {
            throw new Exception('AI failed to return a valid JSON array of questions.');
        }

        $jsonContent = substr($responseBody, $jsonStart, $jsonEnd - $jsonStart);
        $questions = json_decode($jsonContent, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('Failed to decode AI response as JSON: '.json_last_error_msg());
        }

        return $questions;
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

Source Material:
{$content}

Return ONLY the JSON array. Do not include any explanation or markdown formatting outside the JSON block.
PROMPT;
    }
}
