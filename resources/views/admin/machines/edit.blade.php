@extends('layouts.app')

@section('title', 'Makine Düzenle')

@section('content')
<div class="container">
    <div class="mb-4">
        <a href="{{ route('admin.machines.index') }}" class="text-blue-600" style="text-decoration: none;">← Geri Dön</a>
    </div>

    <div class="card">
        <h1 class="text-2xl font-bold mb-6">Makine Düzenle: {{ $machine->name }}</h1>

        <form method="POST" action="{{ route('admin.machines.update', $machine) }}">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="code" class="label">Makine Kodu *</label>
                <input type="text" id="code" name="code" class="input @error('code') input-error @enderror" 
                       value="{{ old('code', $machine->code) }}" required placeholder="örn: HH-01">
                @error('code')
                <p class="text-sm" style="color: #dc2626; margin-top: 0.25rem;">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-group">
                <label for="name" class="label">Makine Adı *</label>
                <input type="text" id="name" name="name" class="input @error('name') input-error @enderror" 
                       value="{{ old('name', $machine->name) }}" required placeholder="örn: Hadde Hattı 1">
                @error('name')
                <p class="text-sm" style="color: #dc2626; margin-top: 0.25rem;">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-group">
                <label for="location" class="label">Lokasyon</label>
                <input type="text" id="location" name="location" class="input @error('location') input-error @enderror" 
                       value="{{ old('location', $machine->location) }}" placeholder="örn: A Blok">
                @error('location')
                <p class="text-sm" style="color: #dc2626; margin-top: 0.25rem;">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-group">
                <label for="description" class="label">Açıklama</label>
                <textarea id="description" name="description" class="input @error('description') input-error @enderror" 
                          rows="3" placeholder="Makine hakkında detaylı bilgi...">{{ old('description', $machine->description) }}</textarea>
                @error('description')
                <p class="text-sm" style="color: #dc2626; margin-top: 0.25rem;">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-group">
                <label style="display: flex; align-items: center; gap: 0.5rem;">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $machine->is_active) ? 'checked' : '' }}>
                    <span>Makine Aktif</span>
                </label>
            </div>

            <div class="flex justify-between" style="margin-top: 1.5rem;">
                <a href="{{ route('admin.machines.index') }}" class="btn btn-secondary">İptal</a>
                <button type="submit" class="btn btn-primary">Güncelle</button>
            </div>
        </form>
    </div>
</div>
@endsection
