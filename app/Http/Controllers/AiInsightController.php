<?php

namespace App\Http\Controllers;

use App\Models\Answer;
use App\Models\Quiz;
use App\Services\AiGeneratorService;
use Exception;

class AiInsightController extends Controller
{
    /**
     * Generate AI-powered insight dari data quiz menggunakan Gemini.
     */
    public function generate(Quiz $quiz)
    {
        // Kumpulkan data quiz
        $participants = $quiz->participants()->get();
        $finished = $participants->whereNotNull('score');
        $total = $participants->count();
        $totalFinished = $finished->count();
        $completionRate = $total > 0 ? round(($totalFinished / $total) * 100, 1) : 0;
        $avgScore = round($finished->avg('score') ?? 0, 1);
        $passingScore = $quiz->passing_score;
        $passed = $finished->where('score', '>=', $passingScore)->count();
        $failed = $finished->where('score', '<', $passingScore)->count();
        $highest = $finished->max('score') ?? 0;
        $lowest = $finished->min('score') ?? 0;

        $quiz->load('questions.options');
        $finishedIds = $finished->pluck('id')->all();
        $answers = count($finishedIds) > 0
            ? Answer::whereIn('participant_id', $finishedIds)->get()
            : collect();

        $answersByQuestion = $answers->groupBy('question_id');

        // Analisis soal: mana yang paling sulit?
        $questionStats = $quiz->questions->map(function ($q) use ($answersByQuestion) {
            $correct = $q->options->firstWhere('is_correct', true);
            $qAnswers = $answersByQuestion->get($q->id, collect());
            $correctCount = $correct ? $qAnswers->where('option_id', $correct->id)->count() : 0;
            $rate = $qAnswers->count() > 0 ? round(($correctCount / $qAnswers->count()) * 100) : 0;

            return [
                'text' => mb_substr($q->text, 0, 80),
                'correct_rate' => $rate,
                'answered' => $qAnswers->count(),
            ];
        })->sortBy('correct_rate')->values();

        $hardestQuestion = $questionStats->first();
        $easiestQuestion = $questionStats->last();
        $hardestText = $hardestQuestion ? (string) $hardestQuestion['text'] : '-';
        $hardestRate = $hardestQuestion ? (int) $hardestQuestion['correct_rate'] : 0;
        $easiestText = $easiestQuestion ? (string) $easiestQuestion['text'] : '-';
        $easiestRate = $easiestQuestion ? (int) $easiestQuestion['correct_rate'] : 0;

        // Sebaran nilai
        $low = $finished->whereBetween('score', [0, 50])->count();
        $mid = $finished->whereBetween('score', [51, 75])->count();
        $high = $finished->whereBetween('score', [76, 100])->count();

        $hardestTop = $questionStats->take(3)->values();
        $easiestTop = $questionStats->reverse()->take(3)->values();

        $hardestLines = $hardestTop->map(function ($q, $i) {
            $idx = $i + 1;
            $text = (string) ($q['text'] ?? '-');
            $rate = (int) ($q['correct_rate'] ?? 0);
            $answered = (int) ($q['answered'] ?? 0);

            return "{$idx}) \"{$text}\" | benar {$rate}% | dijawab {$answered}";
        })->implode("\n");

        $easiestLines = $easiestTop->map(function ($q, $i) {
            $idx = $i + 1;
            $text = (string) ($q['text'] ?? '-');
            $rate = (int) ($q['correct_rate'] ?? 0);
            $answered = (int) ($q['answered'] ?? 0);

            return "{$idx}) \"{$text}\" | benar {$rate}% | dijawab {$answered}";
        })->implode("\n");

        $meta = [
            'quiz' => [
                'title' => (string) $quiz->title,
                'passing_score' => (int) $passingScore,
            ],
            'participants' => [
                'total' => (int) $total,
                'finished' => (int) $totalFinished,
                'completion_rate' => (float) $completionRate,
                'passed' => (int) $passed,
                'failed' => (int) $failed,
                'avg_score' => (float) $avgScore,
                'highest' => (int) $highest,
                'lowest' => (int) $lowest,
                'distribution' => [
                    'low' => (int) $low,
                    'mid' => (int) $mid,
                    'high' => (int) $high,
                ],
            ],
            'questions' => [
                'hardest' => $hardestTop->map(function ($q) {
                    return [
                        'text' => (string) ($q['text'] ?? '-'),
                        'correct_rate' => (int) ($q['correct_rate'] ?? 0),
                        'answered' => (int) ($q['answered'] ?? 0),
                    ];
                })->values()->all(),
                'easiest' => $easiestTop->map(function ($q) {
                    return [
                        'text' => (string) ($q['text'] ?? '-'),
                        'correct_rate' => (int) ($q['correct_rate'] ?? 0),
                        'answered' => (int) ($q['answered'] ?? 0),
                    ];
                })->values()->all(),
            ],
        ];

        // Build prompt Bahasa Indonesia
        $prompt = <<<PROMPT
Kamu adalah analis hasil pelatihan karyawan yang ahli. Tujuanmu: menjelaskan penyebab utama hasil kuis dan memberi solusi yang bisa langsung dieksekusi.

Data Kuis: {$quiz->title}
Total Peserta: {$total} orang
Sudah Selesai: {$totalFinished} orang
Completion Rate: {$completionRate}%
Rata-rata Nilai: {$avgScore}
Nilai Kelulusan: {$passingScore}
Peserta Lulus: {$passed} orang
Peserta Tidak Lulus: {$failed} orang
Nilai Tertinggi: {$highest}
Nilai Terendah: {$lowest}
Sebaran Nilai: Low (0-50): {$low} | Mid (51-75): {$mid} | High (76-100): {$high}
Soal Tersulit (ringkas): "{$hardestText}" ({$hardestRate}% benar)
Soal Termudah (ringkas): "{$easiestText}" ({$easiestRate}% benar)

3 Soal Tersulit:
{$hardestLines}

3 Soal Termudah:
{$easiestLines}

Output WAJIB dalam format tepat ini (gunakan baris baru/new line):
Ringkasan: (2-3 kalimat)
Diagnosis Utama: (1-2 kalimat: apa penyebab paling mungkin dari data di atas)
Area Perhatian: (2-3 poin singkat dipisah dengan titik koma)
Temuan Data:
- (temuan 1 yang mengaitkan angka/soal)
- (temuan 2)
- (temuan 3)
Rekomendasi Peserta:
- (aksi konkret) — Alasan: (kenapa) — Cara: (langkah singkat)
- (aksi konkret) — Alasan: (kenapa) — Cara: (langkah singkat)
- (aksi konkret) — Alasan: (kenapa) — Cara: (langkah singkat)
Rekomendasi Trainer/Materi:
- (aksi konkret) — Dampak: (apa yang membaik) — Cara: (langkah singkat)
- (aksi konkret) — Dampak: (apa yang membaik) — Cara: (langkah singkat)
Rekomendasi Assessment/Soal:
- (aksi konkret) — Target: (soal/area) — Cara: (langkah singkat)
- (aksi konkret) — Target: (soal/area) — Cara: (langkah singkat)
Rencana 7 Hari:
Hari 1-2: ...
Hari 3-5: ...
Hari 6-7: ...

Kaidah:
- Semua rekomendasi HARUS spesifik dan dapat dieksekusi.
- Jangan mengulang angka persis yang sudah ada kecuali diperlukan.
- Jika completion rate rendah, berikan solusi untuk meningkatkan penyelesaian.
Tulis cukup detail (minimal 250 kata, maksimal 600 kata). Gunakan bahasa formal dan profesional. Hindari kalimat menggantung.
Akhiri output dengan baris: AKHIR_INSIGHT
Pastikan semua heading dan tanda titik dua (:) muncul persis seperti format, tanpa variasi.
PROMPT;

        try {
            $ai = app(AiGeneratorService::class);
            $insight = $ai->generateInsight($prompt, 2048);
        } catch (Exception $e) {
            $insight = $this->fallbackInsight($meta, $hardestLines, $easiestLines);
        }

        $required = [
            'Ringkasan:',
            'Diagnosis Utama:',
            'Area Perhatian:',
            'Temuan Data:',
            'Rekomendasi Peserta:',
            'Rekomendasi Trainer/Materi:',
            'Rekomendasi Assessment/Soal:',
            'Rencana 7 Hari:',
            'AKHIR_INSIGHT',
        ];

        $missing = collect($required)
            ->filter(function ($needle) use ($insight) {
                return ! str_contains($insight, $needle);
            })
            ->values()
            ->all();

        if (count($missing) > 0) {
            $retryPrompt = $prompt."\n\nPERHATIAN: Respons sebelumnya tidak lengkap. Pastikan semua bagian diisi lengkap dan tutup dengan AKHIR_INSIGHT.";

            try {
                $ai = app(AiGeneratorService::class);
                $insight = $ai->generateInsight($retryPrompt, 4096);
            } catch (Exception $e) {
                $insight = $this->fallbackInsight($meta, $hardestLines, $easiestLines);
            }
        }

        if (trim($insight) === '') {
            $insight = $this->fallbackInsight($meta, $hardestLines, $easiestLines);
        }

        return response()->json(['insight' => $insight, 'meta' => $meta]);
    }

    private function fallbackInsight(array $meta, string $hardestLines, string $easiestLines): string
    {
        $title = (string) ($meta['quiz']['title'] ?? 'Kuis');
        $passing = (int) ($meta['quiz']['passing_score'] ?? 0);
        $p = $meta['participants'] ?? [];

        $total = (int) ($p['total'] ?? 0);
        $finished = (int) ($p['finished'] ?? 0);
        $completion = (float) ($p['completion_rate'] ?? 0);
        $passed = (int) ($p['passed'] ?? 0);
        $failed = (int) ($p['failed'] ?? 0);
        $avg = (float) ($p['avg_score'] ?? 0);
        $highest = (int) ($p['highest'] ?? 0);
        $lowest = (int) ($p['lowest'] ?? 0);
        $dist = $p['distribution'] ?? ['low' => 0, 'mid' => 0, 'high' => 0];
        $low = (int) ($dist['low'] ?? 0);
        $mid = (int) ($dist['mid'] ?? 0);
        $high = (int) ($dist['high'] ?? 0);

        $diagnosis = $completion < 70
            ? 'Completion rate rendah mengindikasikan hambatan proses (waktu, akses, atau instruksi) sehingga pemahaman peserta belum terukur secara merata.'
            : ($failed > $passed
                ? 'Mayoritas peserta belum mencapai standar kelulusan, mengindikasikan gap pemahaman pada langkah SOP yang kritis atau materi yang belum cukup praktikal.'
                : 'Secara umum peserta memahami materi, namun masih ada gap pada detail SOP tertentu yang terlihat dari performa pada soal-soal tersulit.');

        $areas = [];
        if ($completion < 70) {
            $areas[] = 'Kepatuhan penyelesaian kuis (completion)';
        }
        if ($avg < $passing) {
            $areas[] = 'Penguatan konsep inti sebelum praktik';
        }
        if ($low > 0) {
            $areas[] = 'Pendampingan peserta skor rendah (remedial)';
        }
        if (count($areas) === 0) {
            $areas = ['Ketelitian membaca instruksi', 'Urutan langkah SOP', 'Validasi kondisi lapangan'];
        }

        $areaText = implode('; ', array_slice($areas, 0, 3));

        $temuan1 = "Completion {$completion}% ({$finished}/{$total}) dan rata-rata {$avg} dibanding passing {$passing}.";
        $temuan2 = "Sebaran nilai: Low {$low}, Mid {$mid}, High {$high}; nilai tertinggi {$highest}, terendah {$lowest}.";
        $temuan3 = "Soal tersulit (3 teratas):\n".$hardestLines."\n\nSoal termudah (3 teratas):\n".$easiestLines;

        $hari12 = $completion < 70
            ? 'Identifikasi hambatan penyelesaian (waktu, perangkat, jaringan, instruksi) dan kirim ulang instruksi + jadwal ulang kuis.'
            : 'Lakukan briefing 20–30 menit untuk merangkum SOP inti dan menekankan titik rawan sesuai soal tersulit.';
        $hari35 = 'Sesi simulasi berbasis skenario (role-play) untuk alur kerja end-to-end; gunakan checklist langkah dan evaluasi 1 putaran.';
        $hari67 = 'Mini-assessment ulang (5–8 soal) fokus pada area tersulit + review hasil; tetapkan aksi remedial untuk peserta dengan skor di bawah passing.';

        return implode("\n", [
            'Ringkasan:',
            "Kuis \"{$title}\" menunjukkan completion rate {$completion}% dengan rata-rata nilai {$avg}. Dari {$finished} peserta yang selesai, {$passed} lulus dan {$failed} belum lulus; hal ini menandakan area SOP tertentu masih perlu penguatan.",
            'Diagnosis Utama:',
            $diagnosis,
            'Area Perhatian:',
            $areaText,
            'Temuan Data:',
            '- '.$temuan1,
            '- '.$temuan2,
            '- '.$temuan3,
            'Rekomendasi Peserta:',
            '- Review ulang SOP 1 halaman (ringkasan langkah + titik kritis). — Alasan: mengurangi kesalahan karena detail prosedur. — Cara: baca ringkasan, lalu tandai 3 langkah rawan dan diskusikan 10 menit dengan atasan langsung.',
            '- Latihan simulasi 2 skenario paling sering terjadi di lapangan. — Alasan: mengubah pemahaman teori menjadi tindakan. — Cara: role-play berpasangan, gunakan checklist, dan catat langkah yang terlewat.',
            '- Ulang kuis singkat fokus soal tersulit setelah remedial. — Alasan: memastikan perbaikan terukur. — Cara: lakukan 5–8 soal fokus, target minimal mencapai passing.',
            'Rekomendasi Trainer/Materi:',
            '- Tambahkan diagram alur SOP + contoh kasus (before/after). — Dampak: peserta cepat memahami urutan dan keputusan. — Cara: buat 1 slide alur + 2 studi kasus, gunakan istilah yang sama dengan lapangan.',
            '- Buat sesi microlearning 10 menit untuk konsep tersulit. — Dampak: menutup gap detail SOP tanpa mengulang semua materi. — Cara: kirim video/infografis singkat, lalu tanya 3 pertanyaan cek pemahaman.',
            'Rekomendasi Assessment/Soal:',
            '- Review soal tersulit untuk memastikan pertanyaan jelas dan opsi tidak ambigu. — Target: 3 soal tersulit. — Cara: revisi redaksi, tambahkan konteks skenario singkat, dan validasi dengan 1 SME.',
            '- Tambahkan soal berbasis alur (urutan langkah) dan keputusan (kondisi A/B). — Target: area SOP yang rawan. — Cara: 2 soal skenario + 1 soal urutan langkah, pastikan kunci jawaban sesuai SOP terbaru.',
            'Rencana 7 Hari:',
            'Hari 1-2: '.$hari12,
            'Hari 3-5: '.$hari35,
            'Hari 6-7: '.$hari67,
            'AKHIR_INSIGHT',
        ]);
    }
}
