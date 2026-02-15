@extends('layouts.app')

@section('title', 'Hata Kodu Detayı')

@section('content')
    <div class="container">
        <div class="mb-4">
            <a href="{{ route('admin.error-codes.index') }}" class="text-blue-600" style="text-decoration: none;">← Geri
                Dön</a>
        </div>

        <div class="card mb-6">
            <h1 class="text-2xl font-bold mb-4">Hata Kodu Detayı: {{ $errorCode->code }}</h1>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <p class="stat-label">Hata Kodu</p>
                    <p class="font-bold text-lg">{{ $errorCode->code }}</p>
                </div>
                <div>
                    <p class="stat-label">Kategori</p>
                    <span class="badge badge-blue">{{ $errorCode->category ?? 'N/A' }}</span>
                </div>
                <div>
                    <p class="stat-label">Hata Adı</p>
                    <p class="font-bold text-lg">{{ $errorCode->name }}</p>
                </div>
                <div>
                    <p class="stat-label">Durum</p>
                    @if($errorCode->is_active)
                        <span class="badge badge-green">Aktif</span>
                    @else
                        <span class="badge badge-red">Pasif</span>
                    @endif
                </div>
                @if($errorCode->description)
                    <div class="md:col-span-2">
                        <p class="stat-label">Açıklama</p>
                        <p>{{ $errorCode->description }}</p>
                    </div>
                @endif
            </div>

            <div class="mt-6">
                <a href="{{ route('admin.error-codes.edit', $errorCode) }}" class="btn btn-secondary">Düzenle</a>
            </div>
        </div>

        @if($errorCode->downtimeRecords->count() > 0)
            <div class="card">
                <h2 class="text-xl font-bold mb-4">Bu Hata Kodu ile Duruş Kayıtları</h2>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Tarih</th>
                                <th>Makine</th>
                                <th>Süre (dk)</th>
                                <th>Durum</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($errorCode->downtimeRecords as $record)
                                <tr>
                                    <td>{{ $record->started_at->format('d/m/Y H:i') }}</td>
                                    <td>{{ $record->machine->code }} - {{ $record->machine->name }}</td>
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