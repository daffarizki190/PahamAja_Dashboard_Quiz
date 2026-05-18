<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ruang Tunggu – {{ $quiz->title }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: linear-gradient(135deg, #4F46E5 0%, #7C3AED 100%);
            color: #fff;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            overflow: hidden;
            text-align: center;
        }

        .waiting-container {
            max-width: 600px;
            padding: 40px;
            animation: fadeUp 0.8s ease;
        }

        .icon-container {
            width: 100px;
            height: 100px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 40px;
            margin: 0 auto 30px;
            box-shadow: 0 0 0 10px rgba(255, 255, 255, 0.05);
            animation: pulse 2s infinite;
        }

        h1 {
            font-size: 32px;
            font-weight: 900;
            margin-bottom: 12px;
        }

        p {
            font-size: 16px;
            color: rgba(255, 255, 255, 0.8);
            line-height: 1.6;
            margin-bottom: 40px;
        }

        .participant-card {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 16px;
            padding: 20px;
            backdrop-filter: blur(10px);
            display: inline-block;
        }

        .participant-card-label {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: rgba(255, 255, 255, 0.6);
            margin-bottom: 8px;
            font-weight: 800;
        }

        .participant-card-title {
            font-size: 18px;
            font-weight: 800;
        }

        @keyframes pulse {
            0% { box-shadow: 0 0 0 0 rgba(255, 255, 255, 0.2); }
            70% { box-shadow: 0 0 0 20px rgba(255, 255, 255, 0); }
            100% { box-shadow: 0 0 0 0 rgba(255, 255, 255, 0); }
        }

        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Decorative background elements */
        .blob {
            position: absolute;
            filter: blur(60px);
            border-radius: 50%;
            z-index: -1;
            opacity: 0.5;
        }
        .blob-1 { width: 300px; height: 300px; background: #EC4899; top: -100px; left: -100px; animation: float 10s infinite ease-in-out alternate; }
        .blob-2 { width: 400px; height: 400px; background: #3B82F6; bottom: -150px; right: -150px; animation: float 12s infinite ease-in-out alternate-reverse; }

        @keyframes float {
            0% { transform: translateY(0) scale(1); }
            100% { transform: translateY(30px) scale(1.1); }
        }
    </style>
</head>
<body>

    <div class="blob blob-1"></div>
    <div class="blob blob-2"></div>

    <div class="waiting-container">
        <div class="icon-container">
            <i class="fa-solid fa-hourglass-half"></i>
        </div>
        
        <h1>Ruang Tunggu</h1>
        <p>Anda sudah terdaftar. Silakan tunggu aba-aba dari Admin.<br>Kuis akan dimulai secara serentak untuk semua peserta.</p>

        <div class="participant-card">
            <div class="participant-card-label">Bergabung Sebagai</div>
            <div class="participant-card-title">
                @php
                    $participantId = session("quiz_in_progress.{$quiz->id}");
                    $participant = \App\Models\Participant::find($participantId);
                @endphp
                {{ $participant ? $participant->name : 'Peserta' }}
            </div>
            @if($participant && $participant->location)
            <div style="font-size: 12px; color: rgba(255,255,255,0.7); margin-top: 4px;">
                <i class="fa-solid fa-location-dot"></i> {{ $participant->location }}
            </div>
            @endif
        </div>
    </div>

    <script>
        // Polling logic
        const checkStatusUrl = '{{ route("quiz.waiting.status", $quiz->slug, false) }}';
        const redirectUrl = '{{ route("quiz.take", ["quiz" => $quiz->slug, "participant" => $participantId ?? 0], false) }}';

        let isFetchingStatus = false;
        const statusInterval = setInterval(() => {
            if (isFetchingStatus) return;
            isFetchingStatus = true;

            console.log('Checking status...');
            fetch(checkStatusUrl, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'Cache-Control': 'no-cache'
                }
            })
            .then(response => {
                if (!response.ok) throw new Error('Network response was not ok');
                return response.json();
            })
            .then(data => {
                console.log('Current status:', data.status);
                if (data.status === 'active') {
                    console.log('Quiz is active! Stopping polling and reloading...');
                    clearInterval(statusInterval); // STOP polling immediately
                    window.location.reload(); 
                } else if (data.status === 'closed') {
                    console.log('Quiz closed! Stopping polling...');
                    clearInterval(statusInterval); // STOP polling
                    alert('Kuis telah ditutup oleh Admin.');
                    window.location.href = '/';
                }
            })
            .catch(error => console.error('Error fetching status:', error))
            .finally(() => {
                isFetchingStatus = false;
            });
        }, 5000); // Check every 5 seconds (optimized)
    </script>
</body>
</html>
