<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    public function handle(Request $request, Closure $next, $role): Response
    {
        // 1. Jika belum login, lempar ke halaman login
        if (!Auth::check()) {
            return redirect('/login');
        }

        // 2. Ambil data role, jadikan huruf kecil semua, dan bersihkan spasi
        $userRole = strtolower(trim(Auth::user()->role ?? ''));

        // 3. Jika rolenya sudah cocok dengan izin rute, silakan lewat!
        if ($userRole === $role) {
            return $next($request);
        }

        // 4. JIKA TIDAK COCOK, kembalikan ke habitat aslinya
        if ($userRole === 'admin') {
            return redirect('/admin/dashboard');
        } 
        
        if ($userRole === 'farmer') {
            return redirect('/farmer/dashboard');
        }

        $roleSebenarnya = Auth::user()->role ?? 'KOSONG / TIDAK ADA';
        abort(403, 'AKSES DITOLAK. Sistem membaca jabatan Anda di database sebagai: "' . $roleSebenarnya . '"');
    }
}