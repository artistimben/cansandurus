<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Security Configuration - Cansan Duruş Takip Sistemi
    |--------------------------------------------------------------------------
    |
    | Bu dosya sistemin güvenlik ayarlarını içerir.
    | Tüm ayarlar üretim ortamı için optimize edilmiştir.
    |
    */

    // Session timeout (dakika cinsinden)
    'session_timeout' => env('SESSION_TIMEOUT', 30),

    // Login rate limiting (dakikada maksimum deneme sayısı)
    'login_max_attempts' => env('LOGIN_MAX_ATTEMPTS', 5),
    'login_decay_minutes' => env('LOGIN_DECAY_MINUTES', 15),

    // Password policy
    'password' => [
        'min_length' => 12,
        'require_uppercase' => true,
        'require_lowercase' => true,
        'require_numbers' => true,
        'require_special_chars' => true,
    ],

    // 2FA ayarları
    'two_factor' => [
        'enabled' => env('TWO_FACTOR_ENABLED', false),
        'required_for_admin' => env('TWO_FACTOR_REQUIRED_ADMIN', true),
    ],

    // IP binding (aynı session farklı IP'den kullanılmasın)
    'ip_binding' => env('IP_BINDING_ENABLED', false),

    // Activity logging
    'activity_log' => [
        'enabled' => true,
        'log_all_requests' => false, // Sadece önemli işlemler loglanır
        'retention_days' => 365, // 1 yıl saklama
    ],

    // Security headers
    'headers' => [
        'X-Frame-Options' => 'DENY',
        'X-Content-Type-Options' => 'nosniff',
        'X-XSS-Protection' => '1; mode=block',
        'Strict-Transport-Security' => 'max-age=31536000; includeSubDomains',
        'Referrer-Policy' => 'strict-origin-when-cross-origin',
        'Content-Security-Policy' => "default-src 'self'; script-src 'self' 'unsafe-inline' 'unsafe-eval'; style-src 'self' 'unsafe-inline'; img-src 'self' data: https:; font-src 'self' data:;",
    ],
];
