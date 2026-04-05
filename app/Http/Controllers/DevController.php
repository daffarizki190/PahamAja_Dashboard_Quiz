<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DevController extends Controller
{
    // ─── Auth ────────────────────────────────────────────────────────────────

    public function showLogin()
    {
        if (session()->get('dev.authenticated')) {
            return redirect()->route('dev.health');
        }

        return view('dev.login');
    }

    public function login(Request $request)
    {
        $request->validate(['password' => 'required|string']);

        $configured = trim((string) (config('dev.password') ?? env('DEV_PASSWORD', '')));

        if ($configured === '') {
            return back()->with('error', 'DEV_PASSWORD belum dikonfigurasi di Environment Variable.')->withInput();
        }

        $provided = trim((string) $request->input('password'));
        $ok = false;

        if (str_starts_with($configured, '$2y$') || str_starts_with($configured, '$argon2')) {
            $ok = Hash::check($provided, $configured);
        } else {
            $ok = hash_equals($configured, $provided);
        }

        if (! $ok) {
            return back()->with('error', 'Password salah.')->withInput();
        }

        $request->session()->put('dev.authenticated', true);
        $request->session()->regenerate();

        return redirect()->route('dev.health');
    }

    public function logout(Request $request)
    {
        $request->session()->forget('dev.authenticated');
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('dev.login');
    }

    // ─── Health Monitor ───────────────────────────────────────────────────────

    public function health()
    {
        return view('dev.health');
    }

    /**
     * JSON endpoint — performs all health checks and returns structured data.
     */
    public function healthJson()
    {
        $checks = [];

        // 1. Database
        $checks['database'] = $this->checkDatabase();

        // 2. PHP runtime & extensions
        $checks['php'] = $this->checkPhp();

        // 3. Storage / writable paths
        $checks['storage'] = $this->checkStorage();

        // 4. Environment config
        $checks['env'] = $this->checkEnv();

        // 5. AI API (Gemini) — lightweight check via env key presence
        $checks['ai'] = $this->checkAi();

        // 6. Queue / Session
        $checks['session'] = $this->checkSession();

        // Overall status
        $statuses = array_column($checks, 'status');
        if (in_array('red', $statuses)) {
            $overall = 'red';
        } elseif (in_array('yellow', $statuses)) {
            $overall = 'yellow';
        } else {
            $overall = 'green';
        }

        return response()->json([
            'overall' => $overall,
            'timestamp' => now()->toIso8601String(),
            'app_version' => app()->version(),
            'php_version' => PHP_VERSION,
            'environment' => app()->environment(),
            'checks' => $checks,
        ]);
    }

    // ─── Individual Checks ────────────────────────────────────────────────────

    private function checkDatabase(): array
    {
        try {
            $start = microtime(true);
            DB::connection()->getPdo();
            $ping = round((microtime(true) - $start) * 1000, 2);

            // Row counts
            $tables = [];
            foreach (['quizzes', 'questions', 'options', 'participants', 'answers', 'employees'] as $table) {
                try {
                    $tables[$table] = DB::table($table)->count();
                } catch (\Throwable) {
                    $tables[$table] = 'N/A';
                }
            }

            $status = 'green';
            $message = "Koneksi berhasil ({$ping} ms)";
            $suggestions = [];

            if ($ping > 800) {
                $status = 'red';
                $message = "Latency database sangat tinggi ({$ping} ms)";
                $suggestions[] = 'Cek koneksi ke Supabase. Pertimbangkan menggunakan PgBouncer pooling mode = transaction.';
            } elseif ($ping > 300) {
                $status = 'yellow';
                $message = "Latency database lambat ({$ping} ms)";
                $suggestions[] = 'Latency di atas normal. Pastikan DB_HOST di Vercel env mengarah ke region terdekat.';
            }

            return [
                'label' => 'Database (PostgreSQL)',
                'status' => $status,
                'message' => $message,
                'details' => [
                    'driver' => config('database.default'),
                    'host' => config('database.connections.pgsql.host'),
                    'latency_ms' => $ping,
                    'tables' => $tables,
                ],
                'suggestions' => $suggestions,
            ];
        } catch (\Throwable $e) {
            return [
                'label' => 'Database (PostgreSQL)',
                'status' => 'red',
                'message' => 'Koneksi gagal: '.$e->getMessage(),
                'details' => [],
                'suggestions' => [
                    'Periksa variabel DB_HOST, DB_PORT, DB_DATABASE, DB_USERNAME, DB_PASSWORD di Vercel.',
                    'Pastikan IP Vercel di-whitelist di Supabase Database Settings.',
                    'Coba aktifkan "Allow all IPs" sementara untuk debug.',
                ],
            ];
        }
    }

    private function checkPhp(): array
    {
        $extensions = [
            'zip' => extension_loaded('zip'),
            'pdo' => extension_loaded('pdo'),
            'pdo_pgsql' => extension_loaded('pdo_pgsql'),
            'json' => extension_loaded('json'),
            'mbstring' => extension_loaded('mbstring'),
            'openssl' => extension_loaded('openssl'),
            'curl' => extension_loaded('curl'),
            'gd' => extension_loaded('gd'),
            'simplexml' => extension_loaded('simplexml'),
        ];

        $missing = array_keys(array_filter($extensions, fn ($v) => ! $v));
        $suggestions = [];
        $status = 'green';
        $message = 'Semua ekstensi penting tersedia';

        $criticals = ['zip', 'pdo', 'pdo_pgsql', 'json', 'mbstring', 'openssl', 'curl'];
        $criticalMissing = array_intersect($criticals, $missing);

        if (count($criticalMissing) > 0) {
            $status = 'red';
            $message = 'Ekstensi kritis tidak tersedia: '.implode(', ', $criticalMissing);
            $suggestions[] = 'Tambahkan ekstensi ke php.ini di direktori /api: extension='.implode('.so ', $criticalMissing).'.so';
        } elseif (count($missing) > 0) {
            $status = 'yellow';
            $message = 'Beberapa ekstensi tidak tersedia: '.implode(', ', $missing);
            $suggestions[] = 'Ekstensi "gd" tidak tersedia di Vercel — ini normal. Parser PPTX menggunakan ZipArchive sebagai gantinya.';
        }

        return [
            'label' => 'PHP Runtime & Ekstensi',
            'status' => $status,
            'message' => $message,
            'details' => [
                'version' => PHP_VERSION,
                'sapi' => PHP_SAPI,
                'memory_limit' => ini_get('memory_limit'),
                'max_execution_time' => ini_get('max_execution_time'),
                'upload_max_filesize' => ini_get('upload_max_filesize'),
                'extensions' => $extensions,
            ],
            'suggestions' => $suggestions,
        ];
    }

    private function checkStorage(): array
    {
        $paths = [
            'storage/logs' => storage_path('logs'),
            'storage/framework/sessions' => storage_path('framework/sessions'),
            'storage/framework/views' => storage_path('framework/views'),
            'storage/framework/cache' => storage_path('framework/cache'),
        ];

        $results = [];
        $anyFail = false;

        foreach ($paths as $label => $path) {
            $exists = is_dir($path);
            $writable = $exists && is_writable($path);
            $results[$label] = [
                'exists' => $exists,
                'writable' => $writable,
            ];
            if (! $writable) {
                $anyFail = true;
            }
        }

        $status = $anyFail ? 'yellow' : 'green';
        $suggestions = [];

        if ($anyFail) {
            $suggestions[] = 'Di Vercel, storage diarahkan ke /tmp. Pastikan api/index.php mengatur $app->useStoragePath("/tmp/storage").';
            $suggestions[] = 'Vercel bersifat read-only untuk direktori selain /tmp — ini normal.';
        }

        return [
            'label' => 'Storage & Direktori Writable',
            'status' => $status,
            'message' => $anyFail ? 'Beberapa path tidak writable (normal di Vercel/tmp)' : 'Semua path storage writable',
            'details' => $results,
            'suggestions' => $suggestions,
        ];
    }

    private function checkEnv(): array
    {
        $required = [
            'APP_KEY' => config('app.key'),
            'APP_URL' => config('app.url'),
            'DB_HOST' => env('DB_HOST'),
            'DB_DATABASE' => env('DB_DATABASE'),
            'DB_USERNAME' => env('DB_USERNAME'),
            'DB_PASSWORD' => env('DB_PASSWORD'),
            'ADMIN_PASSWORD' => env('ADMIN_PASSWORD'),
            'GEMINI_API_KEY' => env('GEMINI_API_KEY'),
        ];

        $missing = [];
        $results = [];

        foreach ($required as $key => $value) {
            $filled = ! empty($value);
            // Mask sensitive values
            $display = in_array($key, ['APP_KEY', 'DB_PASSWORD', 'ADMIN_PASSWORD', 'GEMINI_API_KEY', 'DEV_PASSWORD'])
                ? ($filled ? '****** ('.strlen((string) $value).' chars)' : '(kosong)')
                : ($filled ? $value : '(kosong)');

            $results[$key] = ['filled' => $filled, 'value' => $display];
            if (! $filled) {
                $missing[] = $key;
            }
        }

        $status = count($missing) > 0 ? 'red' : 'green';
        $suggestions = [];

        foreach ($missing as $key) {
            $suggestions[] = "Set $key di Vercel Dashboard → Settings → Environment Variables.";
        }

        return [
            'label' => 'Konfigurasi Environment',
            'status' => $status,
            'message' => count($missing) > 0
                ? 'Variabel belum diisi: '.implode(', ', $missing)
                : 'Semua variabel environment terisi',
            'details' => $results,
            'suggestions' => $suggestions,
        ];
    }

    private function checkAi(): array
    {
        $apiKey = env('GEMINI_API_KEY', '');
        $model = env('GEMINI_MODEL', 'gemini-1.5-flash');

        if (empty($apiKey)) {
            return [
                'label' => 'Google Gemini AI',
                'status' => 'red',
                'message' => 'GEMINI_API_KEY tidak ditemukan',
                'details' => ['model' => $model, 'key_set' => false],
                'suggestions' => [
                    'Set GEMINI_API_KEY di Vercel Environment Variables.',
                    'Dapatkan API key di https://aistudio.google.com/apikey',
                ],
            ];
        }

        // Lightweight ping — just check if key length looks valid (no actual API call to avoid cost)
        $keyValid = strlen($apiKey) >= 30;

        return [
            'label' => 'Google Gemini AI',
            'status' => $keyValid ? 'green' : 'yellow',
            'message' => $keyValid
                ? "API Key tersedia. Model: {$model}"
                : 'API Key terlalu pendek — mungkin tidak valid',
            'details' => [
                'model' => $model,
                'key_set' => true,
                'key_length' => strlen($apiKey),
            ],
            'suggestions' => $keyValid ? [] : [
                'Pastikan GEMINI_API_KEY yang diset adalah key yang valid dari Google AI Studio.',
            ],
        ];
    }

    private function checkSession(): array
    {
        $driver = config('session.driver');
        $suggestions = [];
        $status = 'green';
        $message = "Session driver: {$driver}";

        if ($driver === 'file') {
            $status = 'yellow';
            $message = 'Session driver "file" tidak kompatibel di Vercel (serverless)';
            $suggestions[] = 'Set SESSION_DRIVER=cookie di Vercel Environment Variables.';
        } elseif ($driver === 'database') {
            $status = 'yellow';
            $message = 'Session driver "database" bisa lambat di serverless';
            $suggestions[] = 'Gunakan SESSION_DRIVER=cookie untuk performa optimal di Vercel.';
        }

        $cache = config('cache.default');
        if ($cache === 'file') {
            $status = 'yellow';
            $suggestions[] = 'Set CACHE_STORE=array di Vercel Environment Variables (filesystem tidak persisten).';
        }

        return [
            'label' => 'Session & Cache',
            'status' => $status,
            'message' => $message,
            'details' => [
                'session_driver' => $driver,
                'cache_store' => $cache,
                'log_channel' => config('logging.default'),
            ],
            'suggestions' => $suggestions,
        ];
    }
}
