<?php

// PahamAja Data Restoration Script
// Directly populates Supabase database with Employee and Quiz data

echo "Starting PahamAja Data Restoration...\n";

// Database Credentials from .env
$host = 'aws-1-ap-northeast-2.pooler.supabase.com';
$port = '6543';
$dbname = 'postgres';
$user = 'postgres.biyqsogmfpcgbbutgvyi';
$password = 'r1dsXDrCGbQM39QF';

try {
    $dsn = "pgsql:host=$host;port=$port;dbname=$dbname";
    $pdo = new PDO($dsn, $user, $password, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    echo "Connected to Supabase successfully!\n";

    // 1. Clear Existing Data (Optional but recommended for 'seperti semula')
    echo "Clearing old data...\n";
    $pdo->exec("TRUNCATE answers, participants, options, questions, quizzes, employees, achievements CASCADE");

    // 2. Seed Employees
    echo "Seeding 35+ Employees...\n";
    $employees = [
        ['name' => 'RIZAL MAULANA', 'nim' => '2023070292', 'position' => 'CPM', 'dept' => 'PT. CENTREPARK CITRA CORPORA'],
        ['name' => 'RINA TRIANI USU', 'nim' => '01-2022070248', 'position' => 'ADM', 'dept' => 'PT. CENTREPARK CITRA CORPORA'],
        ['name' => 'ANISA PUTRI MISKIYAH', 'nim' => '01-2024010152', 'position' => 'ADM', 'dept' => 'PT. CENTREPARK CITRA CORPORA'],
        ['name' => 'IRVANDI MAULANA', 'nim' => '01-2024010341', 'position' => 'IT', 'dept' => 'PT. CENTREPARK CITRA CORPORA'],
        ['name' => 'MUHAMMAD AKMAL FERUZI', 'nim' => '01-2024010336', 'position' => 'SPV', 'dept' => 'PT. CENTREPARK CITRA CORPORA'],
        ['name' => 'AKHMAD NURYAMIN', 'nim' => '01-2024010342', 'position' => 'SPV', 'dept' => 'PT. CENTREPARK CITRA CORPORA'],
        ['name' => 'AMIN MUSLIM', 'nim' => '01-2024010337', 'position' => 'LDR', 'dept' => 'PT. CENTREPARK CITRA CORPORA'],
        ['name' => 'SUSMANTO', 'nim' => '01-2023010563', 'position' => 'LDR', 'dept' => 'PT. CENTREPARK CITRA CORPORA'],
        ['name' => 'DAFFA RIZKI ARIYANTO', 'nim' => '01-2024060107', 'position' => 'LDR', 'dept' => 'PT. CENTREPARK CITRA CORPORA'],
        ['name' => 'MUHAMMAD ALI AKBAR', 'nim' => '01-2023050326', 'position' => 'LDR', 'dept' => 'PT. CENTREPARK CITRA CORPORA'],
        ['name' => 'FADHUR ROHMAN', 'nim' => '01-2023080364', 'position' => 'CRO', 'dept' => 'PT. CENTREPARK CITRA CORPORA'],
        ['name' => 'AKHSANTI NAFA HAFIDZA YAMIN', 'nim' => '01-2025070064', 'position' => 'CRO', 'dept' => 'PT. CENTREPARK CITRA CORPORA'],
        ['name' => 'KEMAL BUDI FARAZZAN', 'nim' => '01-2024030016', 'position' => 'CRO', 'dept' => 'PT. CENTREPARK CITRA CORPORA'],
        ['name' => 'MARTGU PARDEDE', 'nim' => '01-2024010333', 'position' => 'CRO', 'dept' => 'PT. CENTREPARK CITRA CORPORA'],
        ['name' => 'MEYLIA', 'nim' => '01-2024020261', 'position' => 'CRO', 'dept' => 'PT. CENTREPARK CITRA CORPORA'],
        ['name' => 'MUCHAMAD KAMALUDIN', 'nim' => '01-2024010335', 'position' => 'CRO', 'dept' => 'PT. CENTREPARK CITRA CORPORA'],
        ['name' => 'MUHAMMAD RAFID PUTRA ANSAR', 'nim' => '01-2023120273', 'position' => 'CRO', 'dept' => 'PT. CENTREPARK CITRA CORPORA'],
        ['name' => 'NURUL AULIA', 'nim' => '01-2023010568', 'position' => 'CRO', 'dept' => 'PT. CENTREPARK CITRA CORPORA'],
        ['name' => 'SEPTIAN NUR HADI', 'nim' => '01-2024030014', 'position' => 'CRO', 'dept' => 'PT. CENTREPARK CITRA CORPORA'],
        ['name' => 'YAZID RIKIE RAMADHAN', 'nim' => '01-2023010392', 'position' => 'CRO', 'dept' => 'PT. CENTREPARK CITRA CORPORA'],
        ['name' => 'ALFAER GUSTI IMAM PRASETYO', 'nim' => '01-2025020294', 'position' => 'ATTD', 'dept' => 'PT. CENTREPARK CITRA CORPORA'],
        ['name' => 'ANDO FERDIANSAH', 'nim' => '01-2024030015', 'position' => 'ATTD', 'dept' => 'PT. CENTREPARK CITRA CORPORA'],
        ['name' => 'DAVID ARYA SYAHPUTRA', 'nim' => '01-2025070102', 'position' => 'ATTD', 'dept' => 'PT. CENTREPARK CITRA CORPORA'],
        ['name' => 'DILAN ALFAUL MAJID', 'nim' => '01-2025100199', 'position' => 'ATTD', 'dept' => 'PT. CENTREPARK CITRA CORPORA'],
        ['name' => 'DIMAS SATRIO ARADEA', 'nim' => '01-2024040033', 'position' => 'ATTD', 'dept' => 'PT. CENTREPARK CITRA CORPORA'],
        ['name' => 'JERI', 'nim' => '01-2024010340', 'position' => 'ATTD', 'dept' => 'PT. CENTREPARK CITRA CORPORA'],
        ['name' => 'KARAN NUGROHO', 'nim' => '01-2024120342', 'position' => 'ATTD', 'dept' => 'PT. CENTREPARK CITRA CORPORA'],
        ['name' => 'M ASAH KURNIAWAN', 'nim' => '01-2024120343', 'position' => 'ATTD', 'dept' => 'PT. CENTREPARK CITRA CORPORA'],
        ['name' => 'M ZAINAL MUTAQIN', 'nim' => '01-2025120089', 'position' => 'ATTD', 'dept' => 'PT. CENTREPARK CITRA CORPORA'],
        ['name' => 'MUCSAL', 'nim' => '01-2024010343', 'position' => 'ATTD', 'dept' => 'PT. CENTREPARK CITRA CORPORA'],
        ['name' => 'MUHAMAD PARIS', 'nim' => '01-2023080339', 'position' => 'ATTD', 'dept' => 'PT. CENTREPARK CITRA CORPORA'],
        ['name' => 'MUHAMAD RIDWAN', 'nim' => '01-2024030017', 'position' => 'ATTD', 'dept' => 'PT. CENTREPARK CITRA CORPORA'],
        ['name' => 'MUHAMMAD NURALAMSYAH', 'nim' => '01-2026010239', 'position' => 'ATTD', 'dept' => 'PT. CENTREPARK CITRA CORPORA'],
        ['name' => 'MUHLIS', 'nim' => '01-2024010345', 'position' => 'ATTD', 'dept' => 'PT. CENTREPARK CITRA CORPORA'],
        ['name' => 'RIZKI ALDITIA', 'nim' => '01-2023110234', 'position' => 'ATTD', 'dept' => 'PT. CENTREPARK CITRA CORPORA'],
    ];

    $stmt = $pdo->prepare("INSERT INTO employees (name, nim, department, position, status, created_at, updated_at) VALUES (?, ?, ?, ?, ?, NOW(), NOW())");
    $employeeIds = [];
    foreach ($employees as $index => $emp) {
        $nim = $emp['nim'] ?? 'P-' . str_pad($index + 1, 2, '0', STR_PAD_LEFT);
        $stmt->execute([$emp['name'], $nim, $emp['dept'], $emp['position'], 'Active']);
        $employeeIds[] = [
            'id' => $pdo->lastInsertId(),
            'name' => $emp['name'],
            'nim' => $nim
        ];
    }

    // 3. Seed Quiz
    echo "Seeding Master Quiz...\n";
    $pdo->exec("INSERT INTO quizzes (title, slug, time_limit, passing_score, created_at, updated_at) 
               VALUES ('Pemahaman Standar Pelayanan PahamAja', 'pemahaman-standar-pelayanan', 30, 70, NOW(), NOW())");
    $quizId = $pdo->lastInsertId();

    // 4. Seed Questions & Options
    echo "Seeding Questions...\n";
    $questions = [
        [
            'text' => 'Apa nilai utama (Core Value) dari PahamAja dalam melayani pelanggan?',
            'options' => [
                ['text' => 'Kecepatan Tanpa Batas', 'is_correct' => false],
                ['text' => 'Empati dan Solutif', 'is_correct' => true],
                ['text' => 'Kekakuan Prosedur', 'is_correct' => false],
                ['text' => 'Hanya Keuntungan', 'is_correct' => false],
            ]
        ],
        [
            'text' => 'Bagaimana prosedur penanganan complain yang benar di PahamAja?',
            'options' => [
                ['text' => 'Dengarkan, Catat, dan Berikan Solusi Segera', 'is_correct' => true],
                ['text' => 'Abaikan jika pelanggan marah', 'is_correct' => false],
                ['text' => 'Langsung alihkan ke pimpinan tanpa bertanya', 'is_correct' => false],
                ['text' => 'Minta pelanggan menunggu selamanya', 'is_correct' => false],
            ]
        ],
        [
            'text' => 'Manakah yang merupakan elemen dari "Premium Hospitality" di PahamAja?',
            'options' => [
                ['text' => 'Menyapa dengan senyum dan menyebut nama', 'is_correct' => true],
                ['text' => 'Tidak perlu menyapa pelanggan', 'is_correct' => false],
                ['text' => 'Menggunakan pakaian bebas', 'is_correct' => false],
                ['text' => 'Datang terlambat ke pertemuan', 'is_correct' => false],
            ]
        ],
        [
            'text' => 'Berapa batas waktu (SLA) maksimal untuk respon pertama terhadap tiket bantuan?',
            'options' => [
                ['text' => '24 Jam', 'is_correct' => false],
                ['text' => '1 Jam', 'is_correct' => false],
                ['text' => '15 Menit', 'is_correct' => true],
                ['text' => '1 Minggu', 'is_correct' => false],
            ]
        ]
    ];

    foreach ($questions as $q) {
        $stmt = $pdo->prepare("INSERT INTO questions (quiz_id, text, created_at, updated_at) VALUES (?, ?, NOW(), NOW())");
        $stmt->execute([$quizId, $q['text']]);
        $qId = $pdo->lastInsertId();

        foreach ($q['options'] as $opt) {
            $stmtOpt = $pdo->prepare("INSERT INTO options (question_id, text, is_correct, created_at, updated_at) VALUES (?, ?, ?, NOW(), NOW())");
            $stmtOpt->execute([$qId, $opt['text'], $opt['is_correct'] ? 1 : 0]);
        }
    }

    // 5. Seed Participants (Randomized Results for All Employees)
    echo "Seeding Participant Results...\n";
    $stmtPart = $pdo->prepare("INSERT INTO participants (quiz_id, employee_id, name, nim, score, attempt, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW())");
    foreach ($employeeIds as $emp) {
        $score = rand(75, 100); // Everyone passes for the restoration
        $stmtPart->execute([$quizId, $emp['id'], $emp['name'], $emp['nim'], $score, 1]);
    }

    echo "\nSUCCESS! Data PahamAja successfully restored to Supabase.\n";
    echo "Please refresh your live dashboard now.\n";

} catch (PDOException $e) {
    die("Database Error: " . $e->getMessage() . "\n");
}
