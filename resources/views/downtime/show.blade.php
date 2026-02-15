@extends('layouts.app')

@section('title', 'Duruş Detayı')

@section('content')
<div class="container">
    <div class="mb-4">
        <a href="{{ route('downtime.index') }}" class="text-blue-600" style="text-decoration: none;">← Geri Dön</a>
    </div>

    <div class="card">
        <h1 class="text-2xl font-bold mb-6">Duruş Detayı</h1>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
            <div>
                <p class="stat-label">Makine</p>
                <p class="font-bold text-lg">{{ $downtime->machine->code }} - {{ $downtime->machine->name }}</p>
            </div>
            <div>
                <p class="stat-label">Hata Kodu</p>
                <p class="font-bold text-lg">
                    <span class="badge badge-red">{{ $downtime->errorCode->code }}</span>
                    {{ $downtime->errorCode->name }}
                </p>
            </div>
            <div>
                <p class="stat-label">Başlangıç</p>
                <p class="font-medium">{{ $downtime->started_at->format('d/m/Y H:i') }}</p>
            </div>
            <div>
                <p class="stat-label">Bitiş</p>
                <p class="font-medium">{{ $downtime->ended_at ? $downtime->ended_at->format('d/m/Y H:i') : 'Devam ediyor...' }}</p>
            </div>
            <div>
                <p class="stat-label">Süre</p>
                <p class="font-bold text-lg">
                    @if($downtime->duration_minutes)
                        {{ $downtime->duration_minutes }} dakika
                    @else
                        {{ $downtime->started_at->diffInMinutes(now()) }} dakika (devam ediyor)
                    @endif
                </p>
            </div>
            <div>
                <p class="stat-label">Durum</p>
                @if($downtime->status === 'active')
                <span class="badge badge-red">Aktif</span>
                @elseif($downtime->status === 'completed')
                <span class="badge badge-green">Tamamlandı</span>
                @else
                <span class="badge badge-gray">İptal Edildi</span>
                @endif
            </div>
            <div>
                <p class="stat-label">Başlatan</p>
                <p>{{ $downtime->startedBy->name }}</p>
            </div>
            @if($downtime->endedBy)
            <div>
                <p class="stat-label">Bitiren</p>
                <p>{{ $downtime->endedBy->name }}</p>
            </div>
            @endif
        </div>

        @if($downtime->notes)
        <div class="mb-6">
            <p class="stat-label">Notlar</p>
            <div class="card" style="background-color: #f9fafb;">
                <p>{{ $downtime->notes }}</p>
            </div>
        </div>
        @endif

        <div class="flex gap-4">
            @if($downtime->isActive())
            <form method="POST" action="{{ route('downtime.complete', $downtime) }}">
                @csrf
                <button type="submit" class="btn btn-success" onclick="return confirm('Duruşu bitirmek istediğinize emin misiniz?')">
                    Duruşu Bitir
                </button>
            </form>
            <a href="{{ route('downtime.edit', $downtime) }}" class="btn btn-secondary">Notları Düzenle</a>
            @endif
        </div>
    </div>
</div>
@endsection
