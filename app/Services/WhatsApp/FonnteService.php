<?php

namespace App\Services\WhatsApp;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class FonnteService
{
    public function sendText(string $target, string $message): array
    {
        $baseUrl = rtrim((string) config('services.fonnte.base_url'), '/');
        $token = trim((string) config('services.fonnte.token'));

        if ($token === '') {
            Log::channel('whatsapp')->error('FONNTE_TOKEN kosong / tidak terbaca. Cek .env lalu jalankan php artisan config:clear');

            return ['ok' => false, 'status' => 0, 'body' => 'FONNTE_TOKEN belum diisi atau tidak terbaca.'];
        }

        try {
            $response = Http::timeout(30)
                ->withHeaders(['Authorization' => $token])
                ->asForm()
                ->post($baseUrl . '/send', [
                    'target' => $target,
                    'message' => $message,
                    'countryCode' => '62',
                ]);
        } catch (Throwable $e) {
            // Contoh: cURL error 60 (SSL certificate) di XAMPP/Laragon Windows
            Log::channel('whatsapp')->error('Gagal menghubungi Fonnte', [
                'target' => $target,
                'error' => $e->getMessage(),
            ]);

            return ['ok' => false, 'status' => 0, 'body' => 'Koneksi ke Fonnte gagal: ' . $e->getMessage()];
        }

        $body = $response->json() ?? $response->body();

        // Fonnte tetap membalas HTTP 200 walau gagal; penanda suksesnya ada di field "status".
        $ok = $response->successful()
            && is_array($body)
            && filter_var($body['status'] ?? false, FILTER_VALIDATE_BOOLEAN);

        Log::channel('whatsapp')->log($ok ? 'info' : 'error', $ok ? 'Fonnte: pesan terkirim' : 'Fonnte: pesan gagal', [
            'target' => $target,
            'http_status' => $response->status(),
            'response' => $body,
        ]);

        return [
            'ok' => $ok,
            'status' => $response->status(),
            'body' => $body,
        ];
    }
}
