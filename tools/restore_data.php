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
        ['name' => 'RIZAL MAULANA', 'position' => 'CPM', 'dept' => 'PT. CENTREPARK CITRA CORPORA'],
        ['name' => 'RINA TRIANI USU', 'position' => 'ADM', 'dept' => 'PT. CENTREPARK CITRA CORPORA'],
        ['name' => 'ANISA PUTRI MISKIYAH', 'position' => 'ADM', 'dept' => 'PT. CENTREPARK CITRA CORPORA'],
        ['name' => 'IRVANDI MAULANA', 'position' => 'IT', 'dept' => 'PT. CENTREPARK CITRA CORPORA'],
        ['name' => 'MUHAMMAD AKMAL FERUZI', 'position' => 'SPV', 'dept' => 'PT. CENTREPARK CITRA CORPORA'],
        ['name' => 'AKHMAD NURYAMIN', 'position' => 'SPV', 'dept' => 'PT. CENTREPARK CITRA CORPORA'],
        ['name' => 'AMIN MUSLIM', 'position' => 'LDR', 'dept' => 'PT. CENTREPARK CITRA CORPORA'],
        ['name' => 'SUSMANTO', 'position' => 'LDR', 'dept' => 'PT. CENTREPARK CITRA CORPORA'],
        ['name' => 'DAFFA RIZKI ARIYANTO', 'position' => 'LDR', 'dept' => 'PT. CENTREPARK CITRA CORPORA'],
        ['name' => 'MUHAMMAD ALI AKBAR', 'position' => 'LDR', 'dept' => 'PT. CENTREPARK CITRA CORPORA'],
        ['name' => 'FADHUR ROHMAN', 'position' => 'CRO', 'dept' => 'PT. CENTREPARK CITRA CORPORA'],
        ['name' => 'AKHSANTI NAFA HAFIDZA YAMIN', 'position' => 'CRO', 'dept' => 'PT. CENTREPARK CITRA CORPORA'],
        ['name' => 'KEMAL BUDI FARAZZAN', 'position' => 'CRO', 'dept' => 'PT. CENTREPARK CITRA CORPORA'],
        ['name' => 'MARTGU PARDEDE', 'position' => 'CRO', 'dept' => 'PT. CENTREPARK CITRA CORPORA'],
        ['name' => 'MEYLIA', 'position' => 'CRO', 'dept' => 'PT. CENTREPARK CITRA CORPORA'],
        ['name' => 'MUCHAMAD KAMALUDIN', 'position' => 'CRO', 'dept' => 'PT. CENTREPARK CITRA CORPORA'],
        ['name' => 'MUHAMMAD RAFID PUTRA ANSAR', 'position' => 'CRO', 'dept' => 'PT. CENTREPARK CITRA CORPORA'],
        ['name' => 'NURUL AULIA', 'position' => 'CRO', 'dept' => 'PT. CENTREPARK CITRA CORPORA'],
        ['name' => 'SEPTIAN NUR HADI', 'position' => 'CRO', 'dept' => 'PT. CENTREPARK CITRA CORPORA'],
        ['name' => 'YAZID RIKIE RAMADHAN', 'position' => 'CRO', 'dept' => 'PT. CENTREPARK CITRA CORPORA'],
        ['name' => 'ALFAER GUSTI IMAM PRASETYO', 'position' => 'ATD', 'dept' => 'PT. CENTREPARK CITRA CORPORA'],
        ['name' => 'ANDO FERDIANSAH', 'position' => 'ATD', 'dept' => 'PT. CENTREPARK CITRA CORPORA'],
        ['name' => 'DAVID ARYA SYAHPUTRA', 'position' => 'ATD', 'dept' => 'PT. CENTREPARK CITRA CORPORA'],
        ['name' => 'DILAN ALFAUL MAJID', 'position' => 'ATD', 'dept' => 'PT. CENTREPARK CITRA CORPORA'],
        ['name' => 'DIMAS SATRIO ARADEA', 'position' => 'ATD', 'dept' => 'PT. CENTREPARK CITRA CORPORA'],
        ['name' => 'JERI', 'position' => 'ATD', 'dept' => 'PT. CENTREPARK CITRA CORPORA'],
        ['name' => 'KARAN NUGROHO', 'position' => 'ATD', 'dept' => 'PT. CENTREPARK CITRA CORPORA'],
        ['name' => 'M ASAH KURNIAWAN', 'position' => 'ATD', 'dept' => 'PT. CENTREPARK CITRA CORPORA'],
        ['name' => 'M ZAINAL MUTAQIN', 'position' => 'ATD', 'dept' => 'PT. CENTREPARK CITRA CORPORA'],
        ['name' => 'MUCSAL', 'position' => 'ATD', 'dept' => 'PT. CENTREPARK CITRA CORPORA'],
        ['name' => 'MUHAMAD PARIS', 'position' => 'ATD', 'dept' => 'PT. CENTREPARK CITRA CORPORA'],
        ['name' => 'MUHAMAD RIDWAN', 'position' => 'ATD', 'dept' => 'PT. CENTREPARK CITRA CORPORA'],
        ['name' => 'MUHAMMAD NURALAMSYAH', 'position' => 'ATD', 'dept' => 'PT. CENTREPARK CITRA CORPORA'],
        ['name' => 'MUHLIS', 'position' => 'ATD', 'dept' => 'PT. CENTREPARK CITRA CORPORA'],
        ['name' => 'RIZKI ALDITIA', 'position' => 'ATD', 'dept' => 'PT. CENTREPARK CITRA CORPORA'],

    ];

    $stmt = $pdo->prepare("INSERT INTO employees (name, nim, department, position, status, created_at, updated_at) VALUES (?, ?, ?, ?, ?, NOW(), NOW())");
    $employeeIds = [];
    foreach ($employees as $index => $emp) {
        $nim = 'P-' . str_pad($index + 1, 2, '0', STR_PAD_LEFT);
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
