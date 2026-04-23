@echo off
echo ===================================================
echo PAHAM-AJA BACKGROUND WORKER (QUEUE DAEMON)
echo ===================================================
echo Memulai Queue Worker untuk memproses Background Jobs...
echo (Export PDF, AI Insights, dll)
echo Tekan CTRL+C untuk menghentikan worker ini.
echo ---------------------------------------------------
php artisan queue:work
pause
