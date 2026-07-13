<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * =====================================================================
 * ApiAuthMiddleware — Autentikasi Raspberry Pi via Bearer Token
 * =====================================================================
 * 
 * Middleware sederhana untuk memverifikasi token API dari Raspberry Pi.
 * Token dibandingkan dengan SYNC_API_TOKEN di .env.
 * 
 * Cara kerja:
 *   - Pi mengirim header: Authorization: Bearer {token}
 *   - Middleware membandingkan dengan env('SYNC_API_TOKEN')
 *   - Jika tidak cocok, return 401 Unauthorized
 * 
 * Penggunaan di route:
 *   Route::middleware('api.auth')->group(function () { ... });
 * =====================================================================
 */
class ApiAuthMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // IP whitelist (opsional — kosong = semua boleh)
        $allowedIps = config('sync.allowed_ips', '');
        if ($allowedIps) {
            $ipList = array_map('trim', explode(',', $allowedIps));
            if (!in_array($request->ip(), $ipList)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Forbidden — IP tidak diizinkan.',
                ], 403);
            }
        }

        // Bearer token check
        $token = $request->bearerToken();
        $expectedToken = config('sync.api_token') ?: config('app.sync_api_token');

        if (!$token || !$expectedToken || $token !== $expectedToken) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized — Token API tidak valid.',
            ], 401);
        }

        return $next($request);
    }
}
