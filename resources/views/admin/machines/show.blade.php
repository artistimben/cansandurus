@extends('layouts.app')

@section('title', 'Makine Detayı')

@section('content')
<div class="container">
    <div class="mb-4">
        <a href="{{ route('admin.machines.index') }}" class="text-blue-600" style="text-decoration: none;">← Geri Dön</a>
    </div>

    <div class="card mb-6">
        <h1 class="text-2xl font-bold mb-4">Makine Detayı: {{ $machine->name }}</h1>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <p class="stat-label">Makine Kodu</p>
                <p class="font-bold text-lg">{{ $machine->code }}</p>
            </div>
            <div>
                <p class="stat-label">Makine Adı</p>
                <p class="font-bold text-lg">{{ $machine->name }}</p>
            </div>
            <div>
                <p class="stat-label">Lokasyon</p>
                <p class="font-medium">{{ $machine->location ?? '-' }}</p>
            </div>
            <div>
                <p class="stat-label">Durum</p>
                @if($machine->is_active)
                <span class="badge badge-green">Aktif</span>
                @else
                <span class="badge badge-red">Pasif</span>
                @endif
            </div>
            @if($machine->description)
            <div class="md:col-span-2">
                <p class="stat-label">Açıklama</p>
                <p>{{ $machine->description }}</p>
            </div>
            @endif
        </div>

        <div class="mt-6">
            <a href="{{ route('admin.machines.edit', $machine) }}" class="btn btn-secondary">Düzenle</a>
        </div>
    </div>

    @if($machine->downtimeRecords->count() > 0)
    <div class="card">
        <h2 class="text-xl font-bold mb-4">Son Duruş Kayıtları</h2>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Tarih</th>
                        <th>Hata Kodu</th>
                        <th>Süre (dk)</th>
                        <th>Durum</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($machine->downtimeRecords as $record)
                    <tr>
                        <td>{{ $record->started_at->format('d/m/Y H:i') }}</td>
                        <td>{{ $record->errorCode->code }} - {{ $record->errorCode->name }}</td>
                        <td>{{ $record->duration_minutes ?? '-' }}</td>
                        <td>
                            @if($record->status === 'active')
                            <span class="badge badge-red">Aktif</span>
                            @else
                            <span class="badge badge-green">Tamamlandı</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>
@endsection
