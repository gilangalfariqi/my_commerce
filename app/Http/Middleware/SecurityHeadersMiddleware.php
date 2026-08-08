<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Security Headers Middleware
 *
 * Menambahkan HTTP security headers ke setiap response:
 * - X-Frame-Options: Cegah clickjacking
 * - X-Content-Type-Options: Cegah MIME sniffing
 * - Referrer-Policy: Kontrol informasi referrer
 * - Permissions-Policy: Batasi akses browser API
 * - Content-Security-Policy: Whitelist sumber konten yang diizinkan
 * - Strict-Transport-Security: Paksa HTTPS (hanya di production)
 */
class SecurityHeadersMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // ── Basic Security Headers ──
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=(), payment=()');

        // ── HSTS — hanya aktif di production dengan HTTPS ──
        if (app()->isProduction()) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains; preload');
        }

        // ── Content-Security-Policy ──
        // Whitelist semua sumber eksternal yang dipakai oleh frontend:
        // - Tailwind CDN (sementara, sampai Vite build diaktifkan)
        // - FontAwesome dari cdnjs
        // - Google Fonts
        // - Alpine.js dari jsdelivr
        // - WhatsApp/external link tidak perlu di CSP (hanya navigasi)
        $csp = implode('; ', [
            "default-src 'self'",

            // Script: self + CDN yang dipakai (inline karena Tailwind config & Alpine)
            "script-src 'self' 'unsafe-inline' 'unsafe-eval' "
                . "https://cdn.tailwindcss.com "
                . "https://cdn.jsdelivr.net "
                . "https://www.googletagmanager.com",

            // Style: self + Google Fonts + FontAwesome
            "style-src 'self' 'unsafe-inline' "
                . "https://fonts.googleapis.com "
                . "https://cdnjs.cloudflare.com",

            // Font: self + Google Fonts + FontAwesome
            "font-src 'self' "
                . "https://fonts.gstatic.com "
                . "https://cdnjs.cloudflare.com",

            // Image: self + data URIs (untuk SVG inline/FA) + semua HTTPS (untuk CDN produk)
            "img-src 'self' data: blob: https:",

            // Fetch/XHR: hanya ke self + WhatsApp API (tidak perlu, redirect saja)
            "connect-src 'self' "
                . "https://fonts.googleapis.com "
                . "https://fonts.gstatic.com",

            // Frame: tidak ada iframe yang diizinkan
            "frame-src 'none'",

            // Object/Plugin: tidak ada
            "object-src 'none'",

            // Base URI: hanya self (cegah base tag injection)
            "base-uri 'self'",

            // Form action: hanya self (cegah form hijacking)
            "form-action 'self'",
        ]);

        $response->headers->set('Content-Security-Policy', $csp);

        return $response;
    }
}
