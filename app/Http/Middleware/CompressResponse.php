<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Middleware kompresi respons HTTP (Gzip) untuk memperkecil ukuran transfer data
 * dan mempercepat perpindahan rute/halaman antarmuka.
 */
class CompressResponse
{
    public function handle(Request $request, Closure $next): Response
    {
        /** @var Response $response */
        $response = $next($request);

        // Jangan proses respons streamed atau binary file download
        if ($response instanceof BinaryFileResponse || $response instanceof StreamedResponse) {
            return $response;
        }

        // Jangan proses jika sudah terkompresi atau zlib aktif di php.ini
        if ((bool) ini_get('zlib.output_compression') || $response->headers->has('Content-Encoding')) {
            return $response;
        }

        // Periksa dukungan gzip dari klien
        $acceptEncoding = (string) $request->header('Accept-Encoding', '');
        if (! str_contains($acceptEncoding, 'gzip') || ! function_exists('gzencode')) {
            return $response;
        }

        // Validasi tipe konten yang layak dikompresi
        $contentType = (string) $response->headers->get('Content-Type', '');
        if (! $this->shouldCompress($contentType)) {
            return $response;
        }

        $content = $response->getContent();
        if ($content === false || strlen($content) < 512) {
            return $response;
        }

        $compressed = gzencode($content, 6);
        if ($compressed !== false && strlen($compressed) < strlen($content)) {
            $response->setContent($compressed);
            $response->headers->set('Content-Encoding', 'gzip');
            $response->headers->set('Content-Length', (string) strlen($compressed));
            $response->headers->set('Vary', 'Accept-Encoding', false);
        }

        return $response;
    }

    private function shouldCompress(string $contentType): bool
    {
        if ($contentType === '') {
            return true;
        }

        $compressibleTypes = [
            'text/html',
            'text/plain',
            'text/css',
            'text/javascript',
            'application/javascript',
            'application/json',
            'application/xml',
            'image/svg+xml',
        ];

        foreach ($compressibleTypes as $type) {
            if (str_starts_with($contentType, $type)) {
                return true;
            }
        }

        return false;
    }
}
