@extends('layouts.app')

@section('title', 'Jawaban Peserta – ' . $quiz->title)
@section('meta_description', 'Detail jawaban dan analisis skor peserta kuis')
@section('page_title', 'Jawaban Peserta')
@section('page_subtitle', $quiz->title)

@section('topbar_left')
    <a href="{{ route('admin.quiz.dashboard', $quiz->slug) }}" class="btn btn-ghost" style="padding:9px 12px; font-size:12px; background:rgba(124,58,237,0.08); border:1px solid rgba(124,58,237,0.15); color:var(--purple); margin-right:8px;">
        <i class="fa-solid fa-arrow-left"></i> Dashboard Kuis
    </a>
@endsection

@section('topbar_actions')
    <div style="background:#fff; border:1px solid #E5E3F0; border-radius:10px; padding:4px; display:flex; align-items:center; gap:4px; shadow:0 1px 2px rgba(0,0,0,0.05);">
        <button type="button" id="btnTable" class="btn btn-primary" style="padding:6px 14px; font-size:11px; border-radius:7px;">Table View</button>
        <button type="button" id="btnCard" class="btn btn-ghost" style="padding:6px 14px; font-size:11px; border-radius:7px;">Card View</button>
    </div>
@endsection

@section('head_extra')
<style>
    .score-card {
        background: #fff; border: 1px solid #E5E3F0; border-radius: 16px; padding: 24px;
        display: flex; align-items: center; justify-content: space-between; margin-bottom: 24px;
    }
    .participant-info { display: flex; align-items: center; gap: 16px; }
    .avatar-circle {
        width: 48px; height: 48px; border-radius: 50%; background: #F3F2FB; color: var(--purple);
        display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 18px;
    }
    
    .status-pill {
        padding: 4px 12px; border-radius: 8px; font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.05em;
    }
    .status-qualified { background: rgba(16,185,129,0.1); color: #059669; }
    .status-failed { background: rgba(239,68,68,0.1); color: #DC2626; }
    
    table { width: 100%; border-collapse: collapse; }
    th { padding: 14px 20px; font-size: 11px; font-weight: 800; color: #94A3B8; text-transform: uppercase; text-align: left; border-bottom: 1px solid #F3F2FB; }
    td { padding: 18px 20px; font-size: 14px; color: #1E1B4B; border-bottom: 1px solid #F3F2FB; vertical-align: top; }
    tr:last-child td { border-bottom: none; }
    
    .ans-card {
        background: #fff; border: 1px solid #E5E3F0; border-radius: 14px; padding: 20px; margin-bottom: 16px;
    }
    .status-icon { width: 20px; height: 20px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 11px; }
    .status-correct { background: rgba(16,185,129,0.1); color: #10B981; }
    .status-wrong { background: rgba(239,68,68,0.1); color: #EF4444; }
    .status-empty { background: #F3F4F6; color: #9CA3AF; }
</style>
@endsection

@section('content')
<div class="fade-up">
    <!-- Header Summary -->
    <div class="score-card shadow-sm">
        <div class="participant-info">
            <div class="avatar-circle">{{ $participant->name[0] }}</div>
            <div>
                <h3 style="font-size:18px; font-weight:900; color:#1E1B4B; margin:0;">{{ $participant->name }}</h3>
                <p style="font-size:12px; font-weight:700; color:#94A3B8; margin:2px 0 0 0;">NIM: {{ $participant->nim }} · Attempt: {{ $participant->attempt ?? '1' }}</p>
            </div>
        </div>
        
        <div style="display:flex; align-items:center; gap:32px;">
            <div style="text-align:right;">
                <div style="font-size:11px; font-weight:800; color:#94A3B8; text-transform:uppercase; margin-bottom:4px;">Metadata Sesi</div>
                <div style="font-size:12px; font-weight:700; color:#475569;">
                    <i class="fa-solid fa-network-wired" style="color:var(--purple); margin-right:4px;"></i> {{ $participant->ip_address ?? 'N/A' }}<br>
                    <span style="font-size:10px; color:#94A3B8; font-weight:600;">{{ Str::limit($participant->user_agent, 40) }}</span>
                </div>
            </div>
            <div style="text-align:right;">
                <div style="font-size:11px; font-weight:800; color:#94A3B8; text-transform:uppercase; margin-bottom:4px;">Hasil Skor</div>
                <div style="font-size:28px; font-weight:900; color:var(--purple);">{{ $participant->score ?? '-' }}</div>
            </div>
            <div style="text-align:right;">
                <div style="font-size:11px; font-weight:800; color:#94A3B8; text-transform:uppercase; margin-bottom:4px;">Status</div>
                @if(($participant->score ?? 0) >= ($quiz->passing_score ?? 70))
                    <span class="status-pill status-qualified">Qualified</span>
                @else
                    <span class="status-pill status-failed">Failed</span>
                @endif
            </div>
        </div>
    </div>

    <!-- Table View -->
    <div id="tableView" class="card" style="padding:0; overflow-x:auto;">
        <table>
            <thead>
                <tr>
                    <th style="width:50px;">#</th>
                    <th>Pertanyaan</th>
                    <th style="width:200px;">Jawaban Peserta</th>
                    <th style="width:200px;">Kunci Jawaban</th>
                    <th style="width:100px; text-align:right;">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($rows as $i => $row)
                <tr>
                    <td style="font-weight:700; color:#94A3B8;">{{ $i + 1 }}</td>
                    <td style="font-weight:700; line-height:1.5;">{{ $row['question'] }}</td>
                    <td style="font-size:13px; font-weight:600;">{{ $row['selected'] ?: '-' }}</td>
                    <td style="font-size:13px; font-weight:600; color:#059669;">{{ $row['correct'] ?: '-' }}</td>
                    <td style="text-align:right;">
                        @if(is_null($row['is_correct']))
                            <span style="font-size:11px; font-weight:800; color:#94A3B8;">-</span>
                        @elseif($row['is_correct'])
                            <div style="display:inline-flex; align-items:center; gap:6px; color:#059669; font-weight:800; font-size:12px;">
                                <i class="fa-solid fa-check"></i> BENAR
                            </div>
                        @else
                            <div style="display:inline-flex; align-items:center; gap:6px; color:#DC2626; font-weight:800; font-size:12px;">
                                <i class="fa-solid fa-xmark"></i> SALAH
                            </div>
                        @endif
                    </td>
                </tr>
                @if($row['explanation'] || $row['ai_feedback'])
                <tr style="background:#F9FAFB;">
                    <td></td>
                    <td colspan="4" style="padding:12px 20px;">
                        <div style="display:flex; flex-direction:column; gap:10px;">
                            @if($row['explanation'])
                            <div style="font-size:12px; color:#4B5563;">
                                <strong style="color:var(--purple); display:block; margin-bottom:4px; font-size:11px; text-transform:uppercase;">Pembahasan:</strong>
                                {{ $row['explanation'] }}
                            </div>
                            @endif
                            @if($row['ai_feedback'])
                            <div style="padding:10px; background:rgba(124,58,237,0.05); border-left:3px solid var(--purple); border-radius:4px; font-size:12px; color:#1E1B4B;">
                                <strong style="color:var(--purple); display:block; margin-bottom:4px; font-size:11px; text-transform:uppercase;"><i class="fa-solid fa-wand-magic-sparkles"></i> AI Insight:</strong>
                                {{ $row['ai_feedback'] }}
                            </div>
                            @endif
                        </div>
                    </td>
                </tr>
                @endif
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Card View (Hidden by default) -->
    <div id="cardView" style="display:none; grid-template-columns:repeat(auto-fill, minmax(340px, 1fr)); gap:20px;">
        @foreach($rows as $i => $row)
        <div class="ans-card shadow-sm">
            <div style="display:flex; justify-content:space-between; margin-bottom:12px;">
                <span style="font-size:11px; font-weight:800; color:#94A3B8; text-transform:uppercase;">Soal #{{ $i + 1 }}</span>
                @if(is_null($row['is_correct']))
                    <div class="status-icon status-empty"><i class="fa-solid fa-minus"></i></div>
                @elseif($row['is_correct'])
                    <div class="status-icon status-correct"><i class="fa-solid fa-check"></i></div>
                @else
                    <div class="status-icon status-wrong"><i class="fa-solid fa-xmark"></i></div>
                @endif
            </div>
            <p style="font-size:15px; font-weight:800; color:#1E1B4B; line-height:1.5; margin-bottom:16px;">{{ $row['question'] }}</p>
            
            <div style="display:flex; flex-direction:column; gap:10px;">
                <div style="padding:10px 14px; background:#F9FAFB; border:1px solid #F3F2FB; border-radius:10px;">
                    <div style="font-size:10px; font-weight:800; color:#94A3B8; text-transform:uppercase; margin-bottom:4px;">Jawaban Peserta</div>
                    <div style="font-size:13px; font-weight:700; color:{{ is_null($row['is_correct']) ? '#94A3B8' : ($row['is_correct'] ? '#059669' : '#DC2626') }};">{{ $row['selected'] ?: 'Tidak dijawab' }}</div>
                </div>
            </div>

            @if($row['explanation'] || $row['ai_feedback'])
            <div style="margin-top:16px; padding-top:16px; border-top:1px dashed #E5E3F0; display:flex; flex-direction:column; gap:12px;">
                @if($row['explanation'])
                <div>
                    <div style="font-size:10px; font-weight:800; color:var(--purple); text-transform:uppercase; margin-bottom:4px;">Pembahasan</div>
                    <div style="font-size:12px; color:#4B5563; line-height:1.6;">{{ $row['explanation'] }}</div>
                </div>
                @endif
                @if($row['ai_feedback'])
                <div style="padding:12px; background:rgba(124,58,237,0.05); border-radius:10px; border:1px solid rgba(124,58,237,0.1);">
                    <div style="font-size:10px; font-weight:800; color:var(--purple); text-transform:uppercase; margin-bottom:6px;"><i class="fa-solid fa-wand-magic-sparkles"></i> AI Feedback</div>
                    <div style="font-size:12px; color:#1E1B4B; line-height:1.6;">{{ $row['ai_feedback'] }}</div>
                </div>
                @endif
            </div>
            @endif
        </div>
        @endforeach
    </div>

    <!-- TDD Audit Trail Section -->
    <div class="card shadow-sm" style="margin-top:24px; padding:24px; border:1px solid rgba(124,58,237,0.15); background:linear-gradient(to bottom, #fff, #FBFBFF);">
        <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:20px; border-bottom:1px solid #F3F2FB; padding-bottom:12px;">
            <div style="display:flex; align-items:center; gap:12px;">
                <div style="width:40px; height:40px; border-radius:10px; background:rgba(124,58,237,0.1); color:var(--purple); display:flex; align-items:center; justify-content:center; font-size:18px;">
                    <i class="fa-solid fa-shield-halved"></i>
                </div>
                <div>
                    <h4 style="font-size:15px; font-weight:900; color:#1E1B4B; margin:0;">TDD Audit Trail</h4>
                    <p style="font-size:11px; color:#94A3B8; font-weight:700; margin:0; text-transform:uppercase;">Tracking, Documentation, and Diagnostics</p>
                </div>
            </div>
            <div style="display:flex; gap:16px;">
                <div style="text-align:right;">
                    <div style="font-size:10px; font-weight:800; color:#94A3B8; text-transform:uppercase;">Tab Switches</div>
                    <div style="font-size:16px; font-weight:900; color:{{ $logs->where('event_type', 'TAB_BLUR')->count() > 0 ? '#DC2626' : '#059669' }};">
                        {{ $logs->where('event_type', 'TAB_BLUR')->count() }}
                    </div>
                </div>
            </div>
        </div>

        <div style="max-height:400px; overflow-y:auto; padding-right:10px;">
            <div style="display:flex; flex-direction:column; gap:8px;">
                @forelse($logs as $log)
                    <div style="display:flex; align-items:flex-start; gap:14px; padding:12px; background:#fff; border:1px solid #F3F2FB; border-radius:12px; transition:all .2s;">
                        <div style="width:32px; height:32px; border-radius:8px; display:flex; align-items:center; justify-content:center; font-size:13px; flex-shrink:0;
                            @if($log->event_type == 'SESSION_START') background:rgba(16,185,129,0.1); color:#10B981;
                            @elseif($log->event_type == 'TAB_BLUR') background:rgba(239,68,68,0.1); color:#EF4444;
                            @elseif($log->event_type == 'TAB_FOCUS') background:rgba(59,130,246,0.1); color:#3B82F6;
                            @elseif($log->event_type == 'DISQUALIFIED') background:#1E1B4B; color:#fff;
                            @else background:#F3F2FB; color:#64748B; @endif">
                            @if($log->event_type == 'SESSION_START') <i class="fa-solid fa-play"></i>
                            @elseif($log->event_type == 'TAB_BLUR') <i class="fa-solid fa-eye-slash"></i>
                            @elseif($log->event_type == 'TAB_FOCUS') <i class="fa-solid fa-eye"></i>
                            @elseif($log->event_type == 'QUESTION_VIEW') <i class="fa-solid fa-file-lines"></i>
                            @elseif($log->event_type == 'QUIZ_SUBMIT_START') <i class="fa-solid fa-paper-plane"></i>
                            @elseif($log->event_type == 'DISQUALIFIED') <i class="fa-solid fa-ban"></i>
                            @else <i class="fa-solid fa-circle-dot"></i> @endif
                        </div>
                        <div style="flex:1;">
                            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:2px;">
                                <span style="font-size:12px; font-weight:800; color:#1E1B4B;">{{ str_replace('_', ' ', $log->event_type) }}</span>
                                <span style="font-size:10px; font-weight:700; color:#94A3B8;">{{ $log->created_at->format('H:i:s') }}</span>
                            </div>
                            @if($log->payload)
                                <div style="font-size:11px; color:#64748B; font-weight:600; font-family:monospace; background:#F8FAFC; padding:4px 8px; border-radius:6px; margin-top:4px;">
                                    @foreach($log->payload as $key => $val)
                                        <span style="color:var(--purple)">{{ $key }}:</span> {{ is_array($val) ? json_encode($val) : $val }}@if(!$loop->last) · @endif
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                @empty
                    <div style="text-align:center; padding:32px; color:#94A3B8;">
                        <i class="fa-solid fa-inbox" style="font-size:24px; margin-bottom:12px; display:block;"></i>
                        <p style="font-size:12px; font-weight:700;">Belum ada log aktivitas untuk peserta ini.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    const btnTable = document.getElementById('btnTable');
    const btnCard = document.getElementById('btnCard');
    const tableView = document.getElementById('tableView');
    const cardView = document.getElementById('cardView');

    function setView(mode) {
        if (mode === 'table') {
            tableView.style.display = 'block';
            cardView.style.display = 'none';
            btnTable.className = 'btn btn-primary';
            btnCard.className = 'btn btn-ghost';
        } else {
            tableView.style.display = 'none';
            cardView.style.display = 'grid';
            btnTable.className = 'btn btn-ghost';
            btnCard.className = 'btn btn-primary';
        }
    }

    btnTable.addEventListener('click', () => setView('table'));
    btnCard.addEventListener('click', () => setView('card'));

    // Default view
    setView('table');
</script>
<script src="{{ asset('js/prevent-double-submit.js') }}"></script>
@endsection
