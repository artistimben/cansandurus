@extends('layouts.app')

@section('title', 'Yeni Hata Kodu Ekle')

@section('content')
<div class="container">
    <div class="mb-4">
        <a href="{{ route('admin.error-codes.index') }}" class="text-blue-600" style="text-decoration: none;">← Geri Dön</a>
    </div>

    <div class="card">
        <h1 class="text-2xl font-bold mb-6">Yeni Hata Kodu Ekle</h1>

        <form method="POST" action="{{ route('admin.error-codes.store') }}">
            @csrf

            <div class="form-group">
                <label for="code" class="label">Hata Kodu *</label>
                <input type="text" id="code" name="code" class="input @error('code') input-error @enderror" 
                       value="{{ old('code') }}" required placeholder="örn: M-001">
                @error('code')
                <p class="text-sm" style="color: #dc2626; margin-top: 0.25rem;">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-group">
                <label for="category" class="label">Kategori *</label>
                <input type="text" id="category" name="category" class="input @error('category') input-error @enderror" 
                       value="{{ old('category') }}" required placeholder="örn: Mekanik, Elektrik, Kalite">
                @error('category')
                <p class="text-sm" style="color: #dc2626; margin-top: 0.25rem;">{{ $message }}</p>
                @enderror
                <p class="text-sm text-gray-500 mt-1">Mevcut kategoriler: Mekanik, Elektrik, Kalite, Operasyonel, Planlı</p>
            </div>

            <div class="form-group">
                <label for="name" class="label">Hata Adı *</label>
                <input type="text" id="name" name="name" class="input @error('name') input-error @enderror" 
                       value="{{ old('name') }}" required placeholder="örn: Rulman Arızası">
                @error('name')
                <p class="text-sm" style="color: #dc2626; margin-top: 0.25rem;">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-group">
                <label for="description" class="label">Açıklama</label>
                <textarea id="description" name="description" class="input @error('description') input-error @enderror" 
                          rows="3" placeholder="Hata hakkında detaylı bilgi...">{{ old('description') }}</textarea>
                @error('description')
                <p class="text-sm" style="color: #dc2626; margin-top: 0.25rem;">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-group">
                <label style="display: flex; align-items: center; gap: 0.5rem;">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                    <span>Hata Kodu Aktif</span>
                </label>
            </div>

            <div class="flex justify-between" style="margin-top: 1.5rem;">
                <a href="{{ route('admin.error-codes.index') }}" class="btn btn-secondary">İptal</a>
                <button type="submit" class="btn btn-primary">Kaydet</button>
            </div>
        </form>
    </div>
</div>
@endsection
