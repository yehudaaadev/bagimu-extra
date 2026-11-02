<?php

namespace App\Services;

use App\Models\AppSetting;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FonnteService
{
    public const SETTING_TOKEN = 'whatsapp.fonnte_token';
    public const SETTING_DEVICE = 'whatsapp.fonnte_device';
    public const SETTING_NAME = 'whatsapp.fonnte_name';
    public const SETTING_STATUS = 'whatsapp.fonnte_status';
    public const SETTING_QUOTA = 'whatsapp.fonnte_quota';
    public const SETTING_PACKAGE = 'whatsapp.fonnte_package';
    public const SETTING_EXPIRED = 'whatsapp.fonnte_expired';

    /**
     * Validasi token ke endpoint Fonnte dan ambil info device.
     */
    public function validateToken(string $token): array
    {
        $token = trim($token);
        if ($token === '') {
            return [
                'valid' => false,
                'message' => 'Token Fonnte tidak boleh kosong.',
            ];
        }

        try {
            $response = Http::timeout(10)
                ->withHeaders([
                    'Authorization' => $token,
                ])
                ->post('https://api.fonnte.com/device');

            $data = $response->json();

            if ($response->successful() && isset($data['status']) && $data['status'] === true) {
                return [
                    'valid' => true,
                    'device' => $data['device'] ?? '—',
                    'name' => $data['name'] ?? 'Device Fonnte',
                    'device_status' => $data['device_status'] ?? ($data['status'] ? 'connect' : 'disconnect'),
                    'package' => $data['package'] ?? '—',
                    'quota' => $data['quota'] ?? 0,
                    'expired' => $data['expired'] ?? '—',
                    'message' => 'Token valid & terhubung ke Fonnte.',
                ];
            }

            $reason = $data['reason'] ?? $data['message'] ?? 'Token tidak valid atau device belum terdaftar.';

            return [
                'valid' => false,
                'message' => 'Gagal validasi Fonnte: '.$reason,
            ];
        } catch (\Throwable $e) {
            Log::warning('Fonnte token validation error: '.$e->getMessage());

            return [
                'valid' => false,
                'message' => 'Tidak dapat menghubungi server Fonnte (koneksi timeout / error).',
            ];
        }
    }

    /**
     * Ambil plain token (prioritas .env FONNTE_TOKEN / config, lalu terenkripsi dari AppSetting).
     */
    public function getToken(): ?string
    {
        $envToken = env('FONNTE_TOKEN') ?: config('services.fonnte.token');
        if (! empty($envToken)) {
            return trim((string) $envToken);
        }

        $encrypted = AppSetting::get(self::SETTING_TOKEN);
        if (! $encrypted) {
            return null;
        }

        try {
            return Crypt::decryptString($encrypted);
        } catch (DecryptException $e) {
            Log::error('Fonnte token decryption failed: '.$e->getMessage());

            return null;
        }
    }

    /**
     * Simpan token secara terenkripsi beserta info device dan kuota paket.
     */
    public function saveToken(string $token, array $deviceInfo = []): void
    {
        $token = trim($token);
        AppSetting::set(self::SETTING_TOKEN, Crypt::encryptString($token));

        if (! empty($deviceInfo['device'])) {
            AppSetting::set(self::SETTING_DEVICE, (string) $deviceInfo['device']);
        }
        if (! empty($deviceInfo['name'])) {
            AppSetting::set(self::SETTING_NAME, (string) $deviceInfo['name']);
        }
        if (! empty($deviceInfo['device_status'])) {
            AppSetting::set(self::SETTING_STATUS, (string) $deviceInfo['device_status']);
        }
        if (isset($deviceInfo['quota'])) {
            AppSetting::set(self::SETTING_QUOTA, (string) $deviceInfo['quota']);
        }
        if (! empty($deviceInfo['package'])) {
            AppSetting::set(self::SETTING_PACKAGE, (string) $deviceInfo['package']);
        }
        if (! empty($deviceInfo['expired'])) {
            AppSetting::set(self::SETTING_EXPIRED, (string) $deviceInfo['expired']);
        }
    }

    /**
     * Putus koneksi / hapus token Fonnte.
     */
    public function disconnect(): void
    {
        AppSetting::set(self::SETTING_TOKEN, '');
        AppSetting::set(self::SETTING_DEVICE, '');
        AppSetting::set(self::SETTING_NAME, '');
        AppSetting::set(self::SETTING_STATUS, '');
        AppSetting::set(self::SETTING_QUOTA, '');
        AppSetting::set(self::SETTING_PACKAGE, '');
        AppSetting::set(self::SETTING_EXPIRED, '');
    }

    /**
     * Periksa apakah Fonnte sudah terhubung dan ambil summary status untuk UI.
     */
    public function getStatus(): array
    {
        $token = $this->getToken();
        $isConnected = ! empty($token);
        $quotaSetting = AppSetting::get(self::SETTING_QUOTA);
        $quota = $quotaSetting !== null && $quotaSetting !== '' ? (int) $quotaSetting : 1000;
        $isEnv = ! empty(env('FONNTE_TOKEN') ?: config('services.fonnte.token'));

        return [
            'connected' => $isConnected,
            'masked_token' => $isConnected ? substr($token, 0, 4).'••••••••'.substr($token, -4) : null,
            'device' => AppSetting::get(self::SETTING_DEVICE, '—'),
            'name' => AppSetting::get(self::SETTING_NAME, $isEnv ? 'Fonnte (.env)' : '—'),
            'device_status' => AppSetting::get(self::SETTING_STATUS, $isConnected ? 'connect' : 'disconnect'),
            'quota' => $quota,
            'package' => AppSetting::get(self::SETTING_PACKAGE, 'Paket Gratis (1.000 Pesan)'),
            'expired' => AppSetting::get(self::SETTING_EXPIRED, '—'),
            'is_env' => $isEnv,
        ];
    }

    /**
     * Kirim pesan WhatsApp via Fonnte dengan proteksi kuota gratis.
     */
    public function sendMessage(string $target, string $message): array
    {
        $token = $this->getToken();
        if (! $token) {
            return [
                'success' => false,
                'message' => 'Token Fonnte belum dikonfigurasi.',
            ];
        }

        $currentQuota = (int) AppSetting::get(self::SETTING_QUOTA, '1000');
        if (AppSetting::get(self::SETTING_QUOTA) !== null && $currentQuota <= 0) {
            return [
                'success' => false,
                'message' => 'Batas kuota Fonnte habis (0 kuota tersisa). Silakan isi ulang kuota Fonnte.',
            ];
        }

        try {
            $response = Http::timeout(15)
                ->withHeaders([
                    'Authorization' => $token,
                ])
                ->post('https://api.fonnte.com/send', [
                    'target' => $target,
                    'message' => $message,
                ]);

            $data = $response->json();
            $isSuccess = $response->successful() && ($data['status'] ?? false) === true;

            if ($isSuccess) {
                if (isset($data['quota'])) {
                    AppSetting::set(self::SETTING_QUOTA, (string) $data['quota']);
                } elseif ($currentQuota > 0) {
                    AppSetting::set(self::SETTING_QUOTA, (string) max(0, $currentQuota - 1));
                }
            }

            return [
                'success' => $isSuccess,
                'data' => $data,
                'message' => $data['reason'] ?? ($data['status'] ? 'Pesan terkirim' : 'Gagal mengirim pesan'),
            ];
        } catch (\Throwable $e) {
            return [
                'success' => false,
                'message' => 'Koneksi Fonnte error: '.$e->getMessage(),
            ];
        }
    }
}

