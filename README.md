# SIAP-KAK (Sistem Informasi Pengingat Kegiatan Kantor)

## Fitur Utama

- Login Admin
- Dashboard Administrasi
- Manajemen Template Kegiatan
- Manajemen Grup WhatsApp
- Penjadwalan Kegiatan
- Pengiriman Pesan WhatsApp Otomatis
- Riwayat Pengiriman Pesan
- Queue Processing
- Scheduler Otomatis

## Menjalankan aplikasi
Klik dua kali `jalankan-aplikasi.bat`. Akan terbuka 4 jendela, dan **semuanya harus tetap terbuka**:
- Server: `php artisan serve`
- Queue: `php artisan queue:work` (memproses pengiriman WA)
- Scheduler: `php artisan schedule:work` (mengecek jadwal kirim tiap menit)
- Vite: `npm run dev`
