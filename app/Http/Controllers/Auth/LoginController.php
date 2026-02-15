<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

/**
 * Login Controller - Giriş İşlemleri
 * 
 * Rate limiting ve activity logging ile güvenli giriş
 */
class LoginController extends Controller
{
    /**
     * Login sayfasını göster
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Giriş işlemini gerçekleştir
     */
    public function login(Request $request)
    {
        // Rate limiting kontrolü
        $this->checkTooManyFailedAttempts($request);

        // Validation
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        // Giriş denemesi
        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();
            
            $user = Auth::user();

            // Kullanıcı aktif mi kontrol et
            if (!$user->is_active) {
                Auth::logout();
                return back()->withErrors([
                    'email' => 'Hesabınız aktif değil. Lütfen yönetici ile iletişime geçin.',
                ]);
            }

            // Son giriş bilgilerini güncelle
            $user->updateLastLogin($request->ip());

            // Activity log kaydı
            ActivityLog::createLog(
                userId: $user->id,
                action: 'login',
                description: 'Kullanıcı giriş yaptı'
            );

            // Rate limiter'ı temizle
            RateLimiter::clear($this->throttleKey($request));

            return redirect()->intended(route('dashboard'));
        }

        // Başarısız giriş denemesi
        RateLimiter::hit($this->throttleKey($request), 60 * 15); // 15 dakika

        // Activity log - başarısız giriş
        ActivityLog::createLog(
            userId: null,
            action: 'login_failed',
            description: 'Başarısız giriş denemesi: ' . $request->email
        );

        throw ValidationException::withMessages([
            'email' => 'Girilen bilgiler hatalı.',
        ]);
    }

    /**
     * Çıkış işlemi
     */
    public function logout(Request $request)
    {
        $userId = Auth::id();

        // Activity log
        ActivityLog::createLog(
            userId: $userId,
            action: 'logout',
            description: 'Kullanıcı çıkış yaptı'
        );

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Başarıyla çıkış yaptınız.');
    }

    /**
     * Çok fazla başarısız deneme kontrolü
     */
    protected function checkTooManyFailedAttempts(Request $request)
    {
        $maxAttempts = config('security.login_max_attempts', 5);
        $decayMinutes = config('security.login_decay_minutes', 15);

        if (RateLimiter::tooManyAttempts($this->throttleKey($request), $maxAttempts)) {
            $seconds = RateLimiter::availableIn($this->throttleKey($request));
            
            throw ValidationException::withMessages([
                'email' => "Çok fazla başarısız deneme. Lütfen {$seconds} saniye sonra tekrar deneyin.",
            ]);
        }
    }

    /**
     * Rate limiter key oluştur
     */
    protected function throttleKey(Request $request): string
    {
        return strtolower($request->input('email')) . '|' . $request->ip();
    }
}
