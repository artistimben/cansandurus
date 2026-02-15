<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Rol bazlı erişim kontrolü
 * Kullanıcının belirtilen rollere sahip olup olmadığını kontrol eder
 * 
 * Kullanım: Route::middleware('role:admin,manager')
 */
class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles  İzin verilen roller
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        // Kullanıcı giriş yapmış mı?
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Lütfen giriş yapın.');
        }

        $user = auth()->user();

        // Kullanıcı aktif mi?
        if (!$user->is_active) {
            auth()->logout();
            return redirect()->route('login')->with('error', 'Hesabınız aktif değil. Lütfen yönetici ile iletişime geçin.');
        }

        // Kullanıcının rolü izin verilen roller arasında mı?
        if (!in_array($user->role, $roles)) {
            abort(403, 'Bu sayfaya erişim yetkiniz yok.');
        }

        return $next($request);
    }
}
