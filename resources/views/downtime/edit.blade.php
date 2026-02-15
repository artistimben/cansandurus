@extends('layouts.app')

@section('title', 'Duruş Notlarını Düzenle')

@section('content')
<div class="container">
    <div class="mb-4">
        <a href="{{ route('downtime.show', $downtime) }}" class="text-blue-600" style="text-decoration: none;">← Geri Dön</a>
    </div>

    <div class="card">
        <h1 class="text-2xl font-bold mb-6">Duruş Notlarını Düzenle</h1>
        <p class="text-sm text-gray-600 mb-4">
            Makine: <strong>{{ $downtime->machine->code }} - {{ $downtime->machine->name }}</strong> | 
            Hata: <strong>{{ $downtime->errorCode->code }}</strong>
        </p>

        <form method="POST" action="{{ route('downtime.update', $downtime) }}">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="notes" class="label">Notlar</label>
                <textarea id="notes" name="notes" class="input @error('notes') input-error @enderror" 
                          rows="6" placeholder="Duruş hakkında detaylı bilgi girebilirsiniz...">{{ old('notes', $downtime->notes) }}</textarea>
                @error('notes')
                <p class="text-sm" style="color: #dc2626; margin-top: 0.25rem;">{{ $message }}</p>
                @enderror
                <p class="text-sm text-gray-500 mt-1">Maksimum 1000 karakter</p>
            </div>

            <div class="flex justify-between" style="margin-top: 1.5rem;">
                <a href="{{ route('downtime.show', $downtime) }}" class="btn btn-secondary">İptal</a>
                <button type="submit" class="btn btn-primary">Güncelle</button>
            </div>
        </form>
    </div>
</div>
@endsection
