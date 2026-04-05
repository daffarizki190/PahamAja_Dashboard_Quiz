<p align="center">
  <img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="300" alt="Laravel Logo">
</p>

<h1 align="center">PahamAja — Dashboard Kuis Admin</h1>

<p align="center">
  Sistem manajemen kuis berbasis web untuk pelatihan karyawan, dilengkapi fitur pembuatan soal berbantuan AI, ekspor PDF, dan analisis kinerja real-time.
</p>

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-12.x-FF2D20?style=flat&logo=laravel&logoColor=white" alt="Laravel">
  <img src="https://img.shields.io/badge/PHP-8.2-777BB4?style=flat&logo=php&logoColor=white" alt="PHP">
  <img src="https://img.shields.io/badge/Database-PostgreSQL-336791?style=flat&logo=postgresql&logoColor=white" alt="PostgreSQL">
  <img src="https://img.shields.io/badge/Deploy-Vercel-000000?style=flat&logo=vercel&logoColor=white" alt="Vercel">
  <img src="https://img.shields.io/badge/AI-Gemini-4285F4?style=flat&logo=google&logoColor=white" alt="Gemini AI">
</p>

---

## 📋 Tentang Proyek

**PahamAja Dashboard Quiz** adalah platform administrasi kuis internal yang dibangun untuk kebutuhan pelatihan karyawan di Gandaria City. Admin dapat membuat dan mengelola kuis, memantau hasil peserta secara real-time, serta memanfaatkan kecerdasan buatan (Google Gemini) untuk menghasilkan soal secara otomatis dari file materi (PDF, DOCX, PPTX).

### ✨ Fitur Utama

| Fitur | Deskripsi |
|---|---|
| 🧠 **AI Quiz Generator** | Generate soal pilihan ganda otomatis dari file materi menggunakan Google Gemini |
| 📂 **File Parser** | Mendukung upload file PDF, DOCX, dan PPTX sebagai sumber materi soal |
| 📊 **Dashboard Real-time** | Monitoring peserta, skor, dan statistik kuis secara live |
| 📄 **Ekspor PDF** | Cetak laporan hasil kuis per peserta menggunakan DomPDF |
| 📈 **AI Insight** | Analisis kinerja karyawan berbasis AI dengan rekomendasi otomatis |
| 👥 **Manajemen Karyawan** | Kelola data peserta kuis berdasarkan NIK dan divisi |
| 🔒 **Admin Auth** | Sistem autentikasi admin sederhana berbasis cookie session |
| 📱 **Responsive UI** | Desain glassmorphism yang optimal di semua ukuran layar |
| 🔢 **QR Code** | Generate QR Code untuk akses cepat ke halaman kuis |

---

## 🏗️ Tech Stack

- **Backend**: Laravel 12.x (PHP 8.2)
- **Database**: PostgreSQL (via Supabase)
- **Frontend**: Blade Templates, Vanilla CSS, JavaScript
- **AI**: Google Gemini API (`google-gemini-php/client`)
- **PDF**: `barryvdh/laravel-dompdf`
- **File Parsing**: `smalot/pdfparser`, `phpoffice/phpword`, native ZipArchive (PPTX)
- **Export**: `maatwebsite/excel`
- **QR Code**: `simplesoftwareio/simple-qrcode`
- **Deployment**: Vercel (Serverless PHP via `vercel-php`)

---

## 🚀 Instalasi & Menjalankan Lokal

### Prasyarat

- PHP >= 8.2 (dengan ekstensi `zip`, `pdo_pgsql`)
- Composer
- Node.js & npm
- Akses ke database PostgreSQL (misal: Supabase)

### Langkah Instalasi

```bash
# 1. Clone repositori
git clone https://github.com/daffarizki190/PahamAja_Dashboard_Quiz.git
cd PahamAja_Dashboard_Quiz

# 2. Install dependensi PHP
composer install

# 3. Install dependensi frontend
npm install

# 4. Salin file environment
cp .env.example .env

# 5. Generate application key
php artisan key:generate

# 6. Konfigurasi .env (isi DB_* dan GEMINI_API_KEY)
# Edit file .env sesuai kebutuhan

# 7. Jalankan migrasi database
php artisan migrate

# 8. Build aset frontend
npm run build
```

### Menjalankan Server Lokal

```bash
# Jalankan semua sekaligus (server + vite + log)
composer run dev
```

Akses di: `http://127.0.0.1:8000`

---

## ⚙️ Konfigurasi Environment

Variabel penting di file `.env`:

```env
APP_NAME="PahamAja Dashboard Quiz"
APP_ENV=production
APP_URL=https://paham-aja-dashboard-quiz.vercel.app

# Database PostgreSQL (Supabase)
DB_CONNECTION=pgsql
DB_HOST=your-supabase-host
DB_PORT=5432
DB_DATABASE=postgres
DB_USERNAME=postgres
DB_PASSWORD=your-password

# Google Gemini AI
GEMINI_API_KEY=your-gemini-api-key
GEMINI_MODEL=gemini-1.5-flash

# Admin Credentials
ADMIN_USERNAME=admin
ADMIN_PASSWORD=your-password
```

---

## 🌐 Deployment ke Vercel

Proyek ini di-deploy sebagai **Serverless PHP** di Vercel menggunakan runtime `vercel-php@0.6.2`.

### Langkah Deploy

```bash
# Install Vercel CLI
npm i -g vercel

# Login dan deploy
vercel --prod
```

### Catatan Penting untuk Vercel

- Storage dialihkan ke `/tmp` (tidak persisten antar request)
- Session menggunakan driver `cookie`
- Cache menggunakan driver `array`
- Log diarahkan ke `stderr`
- File konten besar (materi kuis) diproses langsung dari upload tanpa menyimpan permanen

---

## 📁 Struktur Direktori Penting

```
app/
├── Http/Controllers/
│   ├── AdminController.php       # Dashboard, manajemen quiz & karyawan
│   ├── AiQuizController.php      # Generate soal dengan AI
│   ├── AiInsightController.php   # Analisis kinerja AI
│   ├── PdfExportController.php   # Ekspor laporan PDF
│   └── QuizController.php        # Alur pengerjaan kuis oleh peserta
├── Services/
│   ├── FileParserService.php     # Parser PDF/DOCX/PPTX
│   └── AiGeneratorService.php    # Integrasi Gemini AI
└── Models/
    ├── Quiz.php
    ├── Question.php
    ├── Option.php
    ├── Participant.php
    ├── Answer.php
    └── Employee.php

api/
└── index.php                     # Entry point Vercel Serverless
```

---

## 🔑 Akses Admin

| URL | Keterangan |
|---|---|
| `/admin/login` | Halaman login admin |
| `/admin/dashboard` | Dashboard utama |
| `/admin/quizzes` | Kelola daftar kuis |
| `/admin/ai-quiz/create` | Buat soal dengan AI |
| `/admin/employees` | Kelola data karyawan |

---

## 🧪 Menjalankan Tests

```bash
composer run test
```

---

## 📄 Lisensi

Proyek ini bersifat privat dan dikembangkan untuk keperluan internal **Gandaria City — PT Pakuwon Jati Tbk**.

---

<p align="center">
  Dibuat dengan ❤️ menggunakan <a href="https://laravel.com">Laravel 12</a> &amp; <a href="https://ai.google.dev">Google Gemini AI</a>
</p>
