@extends('layouts.app')

@section('title', 'Penilaian Esai – ' . $participant->name)
@section('page_title', 'Penilaian Esai')
@section('page_subtitle', 'Tinjau & berikan skor untuk ' . $participant->name)

@section('topbar_left')
    <a href="{{ route('admin.quizzes.show', $quiz->slug) }}" class="btn btn-ghost" style="padding:9px 12px; font-size:12px;">
        <i class="fa-solid fa-arrow-left"></i> Kembali
    </a>
@endsection

@section('head_extra')
<style>
    .review-card { border-radius:18px; border:1px solid #E5E3F0; background:#fff; overflow:hidden; margin-bottom:24px; box-shadow:0 12px 36px rgba(0,0,0,0.03); }
    .review-q-header { padding:20px 24px; background:#F9FAFB; border-bottom:1px solid #E5E3F0; }
    .review-body { padding:24px; }
    .answer-box { background:#F3F2FB; border-radius:14px; padding:20px; font-size:15px; font-weight:600; color:#1E1B4B; line-height:1.6; border:1px solid #E5E3F0; margin-bottom:20px; position:relative; }
    .ideal-box { background:rgba(16,185,129,0.03); border:1px solid rgba(16,185,129,0.1); border-radius:12px; padding:16px; margin-bottom:20px; }
    .score-input-wrap { display:flex; align-items:center; gap:16px; background:#F9FAFB; padding:16px; border-radius:12px; border:1px solid #E5E3F0; }
    .score-circle { width:44px; height:44px; background:#fff; border:2px solid var(--purple); border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:18px; font-weight:900; color:var(--purple); }
</style>
@endsection

@section('content')
<div class="fade-up">
    <!-- Participant Info -->
    <div class="card" style="padding:20px 24px; margin-bottom:24px; display:flex; align-items:center; gap:16px;">
        <div style="width:56px; height:56px; border-radius:16px; background:linear-gradient(135deg,var(--purple),var(--indigo)); display:flex; align-items:center; justify-content:center; color:#fff; font-size:24px; font-weight:900;">
            {{ strtoupper(substr($participant->name, 0, 1)) }}
        </div>
        <div>
            <div style="font-size:18px; font-weight:900; color:var(--text-primary);">{{ $participant->name }}</div>
            <div style="font-size:12px; color:var(--text-muted); font-weight:700;">{{ $participant->nim }} · {{ $participant->department }}</div>
        </div>
        <div style="margin-left:auto; text-align:right;">
            <div style="font-size:11px; font-weight:800; color:var(--text-muted); text-transform:uppercase; letter-spacing:.05em;">Status</div>
            <span class="badge badge-yellow">Menunggu Review</span>
        </div>
    </div>

    <form action="{{ route('admin.participant.review.store', ['quiz' => $quiz->slug, 'participant' => $participant->id]) }}" method="POST">
        @csrf
        @foreach($essayAnswers as $idx => $ans)
        <div class="review-card">
            <div class="review-q-header">
                <div style="display:flex; justify-content:space-between; align-items:center;">
                    <span style="font-size:13px; font-weight:800; color:var(--purple);">PERTANYAAN #{{ $idx + 1 }}</span>
                </div>
                <h3 style="font-size:16px; font-weight:800; color:var(--text-primary); margin-top:8px; line-height:1.5;">
                    {{ $ans->question->text }}
                </h3>
            </div>
            <div class="review-body">
                <div style="font-size:11px; font-weight:800; color:var(--text-muted); text-transform:uppercase; margin-bottom:10px;">Jawaban Peserta:</div>
                <div class="answer-box">
                    {!! nl2br(e($ans->essay_answer)) !!}
                    <i class="fa-solid fa-quote-right" style="position:absolute; bottom:16px; right:20px; opacity:.1; font-size:32px;"></i>
                </div>

                @if($ans->question->ideal_answer)
                <div class="ideal-box">
                    <div style="font-size:11px; font-weight:800; color:#059669; text-transform:uppercase; margin-bottom:8px;">Kunci Jawaban Ideal:</div>
                    <div style="font-size:14px; font-weight:600; color:#065F46; line-height:1.6;">{{ $ans->question->ideal_answer }}</div>
                </div>
                @endif

                <div style="display:grid; grid-template-columns: 200px 1fr; gap:24px;">
                    <div>
                        <label class="form-label">Berikan Skor (0 - 5)</label>
                        <div class="score-input-wrap">
                            <input type="number" name="scores[{{ $ans->id }}]" min="0" max="5" value="0" class="form-input" style="text-align:center; font-size:18px; font-weight:800; height:48px;" oninput="this.nextElementSibling.textContent = this.value">
                            <div class="score-circle">0</div>
                        </div>
                    </div>
                    <div>
                        <label class="form-label">Umpan Balik (Opsional)</label>
                        <textarea name="feedbacks[{{ $ans->id }}]" rows="2" class="form-input" placeholder="Berikan komentar mengapa skor ini diberikan..."></textarea>
                    </div>
                </div>
            </div>
        </div>
        @endforeach

        <div style="background:#fff; border:1px solid #E5E3F0; border-radius:18px; padding:24px; display:flex; justify-content:space-between; align-items:center; margin-top:32px;">
            <div>
                <h4 style="font-size:15px; font-weight:900; color:var(--text-primary);">Selesaikan Penilaian</h4>
                <p style="font-size:12px; color:var(--text-muted);">Sistem akan menghitung otomatis nilai akhir (0-100) berdasarkan skor di atas.</p>
            </div>
            <div style="display:flex; gap:12px;">
                <a href="{{ route('admin.quizzes.show', $quiz->slug) }}" class="btn btn-ghost" style="padding:12px 24px;">Batal</a>
                <button type="submit" class="btn btn-primary" style="padding:12px 32px; background:linear-gradient(135deg,#059669,#0891B2);">
                    <i class="fa-solid fa-circle-check"></i> Simpan Penilaian & Selesaikan
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
