@extends('layouts.app')

@section('title', 'Kullanıcı Düzenle')

@section('content')
<div class="container">
    <div class="mb-4">
        <a href="{{ route('admin.users.index') }}" class="text-blue-600" style="text-decoration: none;">← Geri Dön</a>
    </div>

    <div class="card">
        <h1 class="text-2xl font-bold mb-6">Kullanıcı Düzenle: {{ $user->name }}</h1>

        <form method="POST" action="{{ route('admin.users.update', $user) }}">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="name" class="label">Ad Soyad *</label>
                <input type="text" id="name" name="name" class="input @error('name') input-error @enderror" 
                       value="{{ old('name', $user->name) }}" required>
                @error('name')
                <p class="text-sm" style="color: #dc2626; margin-top: 0.25rem;">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-group">
                <label for="email" class="label">Email *</label>
                <input type="email" id="email" name="email" class="input @error('email') input-error @enderror" 
                       value="{{ old('email', $user->email) }}" required>
                @error('email')
                <p class="text-sm" style="color: #dc2626; margin-top: 0.25rem;">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-group">
                <label for="password" class="label">Yeni Şifre (Değiştirmek istemiyorsanız boş bırakın)</label>
                <input type="password" id="password" name="password" class="input @error('password') input-error @enderror">
                @error('password')
                <p class="text-sm" style="color: #dc2626; margin-top: 0.25rem;">{{ $message }}</p>
                @enderror
                <p class="text-sm text-gray-500 mt-1">Minimum 12 karakter, büyük/küçük harf, rakam ve özel karakter içermelidir.</p>
            </div>

            <div class="form-group">
                <label for="password_confirmation" class="label">Şifre Tekrar</label>
                <input type="password" id="password_confirmation" name="password_confirmation" class="input">
            </div>

            <div class="form-group">
                <label for="role" class="label">Rol *</label>
                <select id="role" name="role" class="input @error('role') input-error @enderror" required>
                    <option value="admin" {{ old('role', $user->role) === 'admin' ? 'selected' : '' }}>Admin</option>
                    <option value="manager" {{ old('role', $user->role) === 'manager' ? 'selected' : '' }}>Manager</option>
                    <option value="operator" {{ old('role', $user->role) === 'operator' ? 'selected' : '' }}>Operator</option>
                    <option value="maintenance" {{ old('role', $user->role) === 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                </select>
                @error('role')
                <p class="text-sm" style="color: #dc2626; margin-top: 0.25rem;">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-group">
                <label style="display: flex; align-items: center; gap: 0.5rem;">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $user->is_active) ? 'checked' : '' }}>
                    <span>Kullanıcı Aktif</span>
                </label>
            </div>

            <div class="flex justify-between" style="margin-top: 1.5rem;">
                <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">İptal</a>
                <button type="submit" class="btn btn-primary">Güncelle</button>
            </div>
        </form>
    </div>
</div>
@endsection
