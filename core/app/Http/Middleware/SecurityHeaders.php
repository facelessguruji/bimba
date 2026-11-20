<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SecurityHeaders {
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next) {
        $response = $next($request);
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        // Baseline CSP chosen to be zero-breakage for this app: inline scripts
        // and styles stay allowed (blade templates rely on them), scripts/
        // frames/connect may use any HTTPS origin (gateway SDKs, maps, fonts),
        // while http:// script injection, plugin <object> embeds, <base> tag
        // hijacking and third-party framing are now blocked.
        $csp = implode('; ', [
            "default-src 'none'",
            "script-src 'self' 'unsafe-inline' https:",
            "style-src 'self' 'unsafe-inline' https:",
            "font-src 'self' data: https:",
            "img-src 'self' data: blob: https:",
            "media-src 'self' https:",
            "connect-src 'self' https: wss:",
            "frame-src 'self' https:",
            "worker-src 'self' blob:",
            "object-src 'none'",
            "base-uri 'self'",
            "frame-ancestors 'self'",
            "form-action 'self' https:",
        ]);
        $response->headers->set('Content-Security-Policy', $csp);
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), usb=(), magnetometer=(), interest-cohort=()');
        $response->headers->remove('X-Powered-By');
        // SAPI-level header (expose_php) is not in the response bag  -  only
        // touch it while no output has been sent (long-lived CLI/test
        // processes would otherwise raise "headers already sent")
        if (!headers_sent()) {
            header_remove('X-Powered-By');
        }
        return $response;
    }
}
