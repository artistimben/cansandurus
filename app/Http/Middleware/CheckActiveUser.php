<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Kullanıcının aktif olup olmadığını kontrol eder
 * Devre dışı bırakılmış kullanıcıları çıkış yapar
 */
class CheckActiveUser
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();
            
            // Kullanıcı aktif değilse çıkış yap
            if (!$user->is_active) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                
                return redirect()->route('login')
                    ->with('error', 'Hesabınız devre dışı bırakıldı. Lütfen yönetici ile iletişime geçin.');
            }
        }

        return $next($request);
    }
}
