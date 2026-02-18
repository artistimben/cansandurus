@extends('layouts.app')

@section('title', 'Kullanıcı Detayı')

@section('content')
    <div class="container">
        <div class="mb-4">
            <a href="{{ route('admin.users.index') }}" class="text-blue-600" style="text-decoration: none;">← Geri Dön</a>
        </div>

        <div class="card mb-6">
            <h1 class="text-2xl font-bold mb-4">Kullanıcı Detayı: {{ $user->name }}</h1>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <p class="stat-label">Ad Soyad</p>
                    <p class="font-bold text-lg">{{ $user->name }}</p>
                </div>
                <div>
                    <p class="stat-label">Email</p>
                    <p class="font-medium">{{ $user->email }}</p>
                </div>
                <div>
                    <p class="stat-label">Rol</p>
                    <span class="badge badge-blue">{{ ucfirst($user->role) }}</span>
                </div>
                <div>
                    <p class="stat-label">Durum</p>
                    @if($user->is_active)
                        <span class="badge badge-green">Aktif</span>
                    @else
                        <span class="badge badge-red">Pasif</span>
                    @endif
                </div>
                <div>
                    <p class="stat-label">Son Giriş</p>
                    <p>{{ $user->last_login_at ? $user->last_login_at->diffForHumans() : 'Hiç giriş yapmadı' }}</p>
                </div>
            </div>

            <div class="mt-6">
                <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-secondary">Düzenle</a>
            </div>
        </div>

        @if($user->downtimeRecordsStarted->count() > 0)
            <div class="card mb-6">
                <h2 class="text-xl font-bold mb-4">Başlattığı Duruşlar</h2>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Tarih</th>
                                <th>Makine</th>
                                <th>Hata Kodu</th>
                                <th>Durum</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($user->downtimeRecordsStarted as $record)
                                <tr>
                                    <td>{{ $record->started_at->format('d/m/Y H:i') }}</td>
                                    <td>{{ $record->machine->code }}</td>
                                    <td>{{ $record->errorCode->code ?? 'N/A' }}</td>
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