<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->redirectGuestsTo(function (\Illuminate\Http\Request $request) {
            if ($request->is('admin*')) {
                return route('admin.login');
            }
            if ($request->is('pembina*') || $request->is('wali-kelas*')) {
                return route('pembina.login');
            }
            return route('siswa.login');
        });

        $middleware->alias([
            'role' => \App\Http\Middleware\EnsureRole::class,
        ]);
        $middleware->web(append: [
            \App\Http\Middleware\CheckSessionVersion::class,
            \App\Http\Middleware\SecurityHeaders::class,
            \App\Http\Middleware\CompressResponse::class,
        ]);

        // Percayai reverse proxy / load balancer bila dikonfigurasi
        // (wajib saat di belakang Nginx proxy / Cloudflare agar
        // deteksi HTTPS dan IP pengunjung benar). Isi TRUSTED_PROXIES
        // di .env, mis. "*" atau "10.0.0.0/8".
        $proxies = env('TRUSTED_PROXIES');
        if (is_string($proxies) && trim($proxies) !== '') {
            $at = trim($proxies) === '*'
                ? '*'
                : array_map('trim', explode(',', $proxies));
            $middleware->trustProxies(at: $at);
        }
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (\Illuminate\Database\QueryException $e, \Illuminate\Http\Request $request) {
            if (! $request->expectsJson() && ! in_array($request->method(), ['GET', 'HEAD'], true)) {
                \Illuminate\Support\Facades\Log::error('Form Database Query Error: ' . $e->getMessage());
                return back()->withInput()->with('error', 'Terjadi kendala saat menyimpan data. Pastikan semua isian wajib telah lengkap dan data tidak duplikat.');
            }
        });
    })->create();
