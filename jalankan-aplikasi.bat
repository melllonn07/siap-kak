@echo off
REM Menjalankan semua proses yang dibutuhkan (web, queue, scheduler, vite)
php artisan config:clear
start "SiapKak - Server" cmd /k php artisan serve
start "SiapKak - Queue (kirim WA)" cmd /k php artisan queue:work --tries=3
start "SiapKak - Scheduler (jadwal WA)" cmd /k php artisan schedule:work
start "SiapKak - Vite" cmd /k npm run dev
