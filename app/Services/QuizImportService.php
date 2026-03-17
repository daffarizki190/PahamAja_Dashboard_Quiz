<?php

namespace App\Services;

use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class QuizImportService
{
    public function parseUploadedQuestions(UploadedFile $file): array
    {
        $ext = strtolower($file->getClientOriginalExtension() ?: '');

        if ($ext === 'json') {
            $raw = $file->get();
            if (! is_string($raw) || trim($raw) === '') {
                throw new Exception('File JSON kosong.');
            }

            $decoded = json_decode($raw, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception('JSON tidak valid: '.json_last_error_msg());
            }

            $questions = $this->normalizeQuestionsFromDecoded($decoded);
            $this->assertQuestionsValid($questions);

            return $questions;
        }

        if ($ext === 'csv') {
            $raw = $file->get();
            if (! is_string($raw) || trim($raw) === '') {
                throw new Exception('File CSV kosong.');
            }

            $questions = $this->parseCsv($raw);
            $this->assertQuestionsValid($questions);

            return $questions;
        }

        throw new Exception('Format file tidak didukung. Gunakan .csv atau .json');
    }

    private function normalizeQuestionsFromDecoded($decoded): array
    {
        if (is_array($decoded) && isset($decoded['questions']) && is_array($decoded['questions'])) {
            $decoded = $decoded['questions'];
        }

        if (! is_array($decoded)) {
            throw new Exception('Struktur JSON tidak valid. Harus array questions.');
        }

        $result = [];
        foreach ($decoded as $i => $q) {
            if (! is_array($q)) {
                throw new Exception('Format soal JSON tidak valid pada index '.($i + 1).'.');
            }

            $text = (string) ($q['text'] ?? $q['question'] ?? '');
            $text = trim($text);
            if ($text === '') {
                throw new Exception('Teks soal kosong pada index '.($i + 1).'.');
            }

            $optionsRaw = $q['options'] ?? $q['choices'] ?? null;
            if (! is_array($optionsRaw) || count($optionsRaw) < 2) {
                throw new Exception('Opsi soal kurang dari 2 pada index '.($i + 1).'.');
            }

            $options = [];
            $correctIndex = null;

            foreach (array_values($optionsRaw) as $oIndex => $o) {
                if (is_string($o)) {
                    $optText = trim($o);
                    $isCorrect = false;
                } elseif (is_array($o)) {
                    $optText = trim((string) ($o['text'] ?? $o['value'] ?? $o['label'] ?? ''));
                    $isCorrect = ($o['is_correct'] ?? $o['correct'] ?? false) === true || ($o['is_correct'] ?? $o['correct'] ?? '0') === '1' || ($o['is_correct'] ?? $o['correct'] ?? 0) === 1;
                } else {
                    $optText = '';
                    $isCorrect = false;
                }

                if ($optText === '') {
                    continue;
                }

                if ($isCorrect) {
                    $correctIndex = $oIndex;
                }

                $options[] = ['text' => $optText, 'is_correct' => $isCorrect];
            }

            if (is_null($correctIndex) && isset($q['correct'])) {
                $correctIndex = $this->resolveCorrectIndex((string) $q['correct'], $options);
            }

            if (is_null($correctIndex) && isset($q['correct_option'])) {
                $correctIndex = $this->resolveCorrectIndex((string) $q['correct_option'], $options);
            }

            if (! is_null($correctIndex)) {
                foreach ($options as $idx => $opt) {
                    $options[$idx]['is_correct'] = $idx === $correctIndex;
                }
            }

            $result[] = [
                'text' => $text,
                'options' => array_values($options),
            ];
        }

        return $result;
    }

    private function parseCsv(string $raw): array
    {
        $raw = str_replace("\r\n", "\n", $raw);
        $raw = str_replace("\r", "\n", $raw);

        $lines = array_values(array_filter(explode("\n", $raw), function ($l) {
            return trim($l) !== '';
        }));

        if (count($lines) < 2) {
            throw new Exception('CSV harus memiliki header dan minimal 1 baris data.');
        }

        $delimiter = $this->detectCsvDelimiter($lines[0]);

        $header = str_getcsv($lines[0], $delimiter);
        $header = array_map(function ($h) {
            return Str::of($h)->lower()->trim()->toString();
        }, $header);

        $map = $this->mapCsvHeader($header);
        $questions = [];

        for ($i = 1; $i < count($lines); $i++) {
            $row = str_getcsv($lines[$i], $delimiter);
            if (count(array_filter($row, fn ($v) => trim((string) $v) !== '')) === 0) {
                continue;
            }

            $questionText = trim((string) ($row[$map['question']] ?? ''));
            if ($questionText === '') {
                continue;
            }

            $optionTexts = [];
            foreach ($map['options'] as $idx) {
                $val = trim((string) ($row[$idx] ?? ''));
                if ($val !== '') {
                    $optionTexts[] = $val;
                }
            }

            if (count($optionTexts) < 2) {
                throw new Exception('Opsi kurang dari 2 pada baris '.($i + 1).'.');
            }

            $options = array_map(function ($t) {
                return ['text' => $t, 'is_correct' => false];
            }, $optionTexts);

            $correctRaw = trim((string) ($row[$map['correct']] ?? ''));
            $correctIndex = $this->resolveCorrectIndex($correctRaw, $options);
            if (is_null($correctIndex)) {
                throw new Exception('Kunci jawaban tidak valid pada baris '.($i + 1).'.');
            }

            foreach ($options as $idx => $opt) {
                $options[$idx]['is_correct'] = $idx === $correctIndex;
            }

            $questions[] = [
                'text' => $questionText,
                'options' => $options,
            ];
        }

        if (count($questions) === 0) {
            throw new Exception('Tidak ada soal yang berhasil dibaca dari CSV.');
        }

        return $questions;
    }

    private function detectCsvDelimiter(string $line): string
    {
        $candidates = [',', ';', "\t"];
        $best = ',';
        $bestCount = -1;

        foreach ($candidates as $d) {
            $count = substr_count($line, $d);
            if ($count > $bestCount) {
                $bestCount = $count;
                $best = $d;
            }
        }

        return $best;
    }

    private function mapCsvHeader(array $header): array
    {
        $find = function (array $aliases) use ($header) {
            foreach ($header as $idx => $h) {
                foreach ($aliases as $a) {
                    if ($h === $a) {
                        return $idx;
                    }
                }
            }

            return null;
        };

        $questionIdx = $find(['question', 'pertanyaan', 'soal', 'text']);
        $correctIdx = $find(['correct', 'answer', 'kunci', 'jawaban_benar', 'correct_option']);

        $optionCandidates = [
            $find(['option_a', 'a', 'pilihan_a', 'choice_a', 'option1', 'pilihan1', 'choice1']),
            $find(['option_b', 'b', 'pilihan_b', 'choice_b', 'option2', 'pilihan2', 'choice2']),
            $find(['option_c', 'c', 'pilihan_c', 'choice_c', 'option3', 'pilihan3', 'choice3']),
            $find(['option_d', 'd', 'pilihan_d', 'choice_d', 'option4', 'pilihan4', 'choice4']),
            $find(['option_e', 'e', 'pilihan_e', 'choice_e', 'option5', 'pilihan5', 'choice5']),
        ];

        $optionIdxs = array_values(array_filter($optionCandidates, fn ($v) => ! is_null($v)));

        if (is_null($questionIdx) || is_null($correctIdx) || count($optionIdxs) < 2) {
            throw new Exception('Header CSV tidak dikenali. Gunakan kolom: question, option_a, option_b, ... , correct');
        }

        return [
            'question' => $questionIdx,
            'options' => $optionIdxs,
            'correct' => $correctIdx,
        ];
    }

    private function resolveCorrectIndex(string $raw, array $options): ?int
    {
        $raw = trim($raw);
        if ($raw === '') {
            return null;
        }

        if (ctype_digit($raw)) {
            $idx = (int) $raw - 1;

            return ($idx >= 0 && $idx < count($options)) ? $idx : null;
        }

        $letters = ['a' => 0, 'b' => 1, 'c' => 2, 'd' => 3, 'e' => 4];
        $key = strtolower($raw);
        if (isset($letters[$key])) {
            $idx = $letters[$key];

            return ($idx >= 0 && $idx < count($options)) ? $idx : null;
        }

        foreach ($options as $idx => $opt) {
            $t = trim((string) ($opt['text'] ?? ''));
            if ($t !== '' && strcasecmp($t, $raw) === 0) {
                return $idx;
            }
        }

        return null;
    }

    private function assertQuestionsValid(array $questions): void
    {
        foreach ($questions as $i => $q) {
            $text = trim((string) ($q['text'] ?? ''));
            if ($text === '') {
                throw new Exception('Teks soal kosong pada nomor '.($i + 1).'.');
            }

            $options = $q['options'] ?? null;
            if (! is_array($options) || count($options) < 2) {
                throw new Exception('Opsi soal kurang dari 2 pada nomor '.($i + 1).'.');
            }

            $correctCount = 0;
            foreach ($options as $o) {
                $ot = trim((string) ($o['text'] ?? ''));
                if ($ot === '') {
                    throw new Exception('Ada opsi kosong pada nomor '.($i + 1).'.');
                }

                if (($o['is_correct'] ?? false) === true) {
                    $correctCount++;
                }
            }

            if ($correctCount !== 1) {
                throw new Exception('Jawaban benar harus tepat 1 pada nomor '.($i + 1).'.');
            }
        }
    }
}
