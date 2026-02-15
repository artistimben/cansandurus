<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Giriş - Cansan Duruş Takip</title>
    
    <!-- Custom CSS -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body>
    <div class="login-container">
        <div class="login-box">
            <!-- Logo ve Başlık -->
            <div class="login-header">
                <h1>CANSAN</h1>
                <h2>Duruş Takip Sistemi</h2>
                <p>Lütfen giriş yapın</p>
            </div>

            <!-- Alerts -->
            @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
            @endif

            @if(session('error'))
            <div class="alert alert-error">
                {{ session('error') }}
            </div>
            @endif

            @if($errors->any())
            <div class="alert alert-error">
                @foreach($errors->all() as $error)
                <p>{{ $error }}</p>
                @endforeach
            </div>
            @endif

            <!-- Login Form -->
            <div class="login-card">
                <form method="POST" action="{{ route('login.post') }}">
                    @csrf

                    <div class="form-group">
                        <label for="email" class="label">Email Adresi</label>
                        <input 
                            id="email" 
                            name="email" 
                            type="email" 
                            autocomplete="email" 
                            required 
                            class="input @error('email') input-error @enderror"
                            value="{{ old('email') }}"
                            placeholder="ornek@cansan.local"
                        >
                    </div>

                    <div class="form-group">
                        <label for="password" class="label">Şifre</label>
                        <input 
                            id="password" 
                            name="password" 
                            type="password" 
                            autocomplete="current-password" 
                            required 
                            class="input @error('password') input-error @enderror"
                            placeholder="••••••••••••"
                        >
                    </div>

                    <div class="form-group flex items-center">
                        <input 
                            id="remember" 
                            name="remember" 
                            type="checkbox" 
                        >
                        <label for="remember" style="margin-left: 0.5rem; margin-bottom: 0;">
                            Beni hatırla
                        </label>
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-primary w-full">
                            Giriş Yap
                        </button>
                    </div>
                </form>
            </div>

            <!-- Güvenlik Bilgisi -->
            <div class="login-footer">
                <p>🔒 Bu sistem yüksek güvenlik standartlarına sahiptir.</p>
                <p>Tüm aktiviteler loglanmaktadır.</p>
            </div>
        </div>
    </div>
</body>
</html>
