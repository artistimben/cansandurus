@extends('layouts.app')

@section('title', 'Dashboard - Cansan Duruş Takip')

@section('content')
    <div class="container">
        <!-- Page Header -->
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold">Dashboard</h1>
            @if(auth()->user()->role !== 'manager')
                <a href="{{ route('downtime.create') }}" class="btn btn-primary">
                    + Yeni Duruş Başlat
                </a>
            @endif
        </div>

        <!-- İstatistik Kartları -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
            <!-- Toplam Makine -->
            <div class="stat-card">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="stat-label">Toplam Makine</p>
                        <p class="stat-number text-blue-600">{{ $stats['total_machines'] }}</p>
                    </div>
                    <div class="stat-icon bg-blue-100">
                        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z">
                            </path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Aktif Duruşlar -->
            <div class="stat-card">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="stat-label">Aktif Duruşlar</p>
                        <p class="stat-number text-red-600">{{ $stats['active_downtimes'] }}</p>
                    </div>
                    <div class="stat-icon bg-red-100">
                        <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                            </path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Bugünkü Duruş -->
            <div class="stat-card">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="stat-label">Bugünkü Duruş (dk)</p>
                        <p class="stat-number text-yellow-600">{{ number_format($stats['today_total_downtime']) }}</p>
                    </div>
                    <div class="stat-icon bg-yellow-100">
                        <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Bugünkü Duruş Sayısı -->
            <div class="stat-card">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="stat-label">Bugünkü Duruş Sayısı</p>
                        <p class="stat-number text-green-600">{{ $stats['today_downtime_count'] }}</p>
                    </div>
                    <div class="stat-icon bg-green-100">
                        <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 00-2-2m0 0h6a2 2 0 002-2V9a2 2 0 00-2-2h-2a2 2 0 00-2 2v6a2 2 0 002 2zm10 0v-9a2 2 0 00-2-2h-2a2 2 0 00-2 2v9a2 2 0 002 2h2a2 2 0 002-2z">
                            </path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Aktif Duruşlar -->
        @if($activeDowntimes->count() > 0)
            <div class="card mb-6 border-l-4 border-red-500">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900">⚠️ Aktif Duruşlar</h2>
                        <p class="text-gray-600 mt-1">Şu anda devam eden duruşlar</p>
                    </div>
                    <div class="px-4 py-2 bg-red-100 rounded-lg border-2 border-red-300">
                        <span class="text-red-700 font-bold text-lg">{{ $activeDowntimes->count() }} AKTİF</span>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                    @foreach($activeDowntimes as $downtime)
                        <div
                            class="bg-white border-2 border-gray-300 rounded-lg p-5 hover:shadow-lg transition-all hover:border-gray-400">
                            <!-- Başlık Satırı -->
                            <div class="flex justify-between items-start mb-4 pb-3 border-b-2 border-gray-200">
                                <div>
                                    <h3 class="text-xl font-bold text-gray-900">🏭 {{ $downtime->machine->code }}</h3>
                                    <p class="text-sm text-gray-600">{{ $downtime->machine->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $downtime->machine->location }}</p>
                                </div>
                                <span
                                    class="px-3 py-1 bg-red-600 text-white text-xs font-bold rounded-full animate-pulse">AKTİF</span>
                            </div>

                            <!-- Hata Kodu -->
                            <div class="mb-4 p-3 bg-gray-50 rounded-lg border border-gray-200">
                                <p class="text-xs text-gray-500 mb-2">Hata Kodu</p>
                                @if($downtime->errorCode)
                                    <div class="flex items-start gap-2">
                                        <span
                                            class="px-2 py-1 bg-red-600 text-white text-sm font-bold rounded">{{ $downtime->errorCode->code }}</span>
                                        <div class="flex-1">
                                            <p class="text-sm font-semibold text-gray-900">{{ $downtime->errorCode->name }}</p>
                                            <span
                                                class="inline-block mt-1 px-2 py-0.5 bg-gray-100 text-gray-600 text-xs rounded">{{ $downtime->errorCode->category ?? 'N/A' }}</span>
                                        </div>
                                    </div>
                                @else
                                    <span class="badge badge-gray">Hata kodu bulunamadı</span>
                                @endif
                            </div>

                            <!-- Zaman Bilgileri -->
                            <div class="grid grid-cols-2 gap-3 mb-4">
                                <!-- Başlangıç -->
                                <div class="p-3 bg-gray-50 rounded-lg border border-gray-200">
                                    <p class="text-xs text-gray-500 mb-1">Başlangıç</p>
                                    <p class="text-sm font-bold text-gray-900">{{ $downtime->started_at->format('d.m.Y') }}</p>
                                    <p class="text-xs text-gray-600">{{ $downtime->started_at->format('H:i') }}</p>
                                </div>

                                <!-- Geçen Süre -->
                                <div class="p-3 bg-gray-50 rounded-lg border border-gray-200">
                                    <p class="text-xs text-gray-500 mb-1">Geçen Süre</p>
                                    <div class="live-timer font-mono font-bold text-xl text-gray-900"
                                        data-start="{{ $downtime->started_at->timestamp }}" id="timer-{{ $downtime->id }}">
                                        00:00:00
                                    </div>
                                </div>
                            </div>

                            <!-- Başlatan Kişi -->
                            <div class="mb-4 p-3 bg-gray-50 rounded-lg border border-gray-200 flex items-center gap-2">
                                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                                <p class="text-xs text-gray-500">Başlatan:</p>
                                <p class="text-sm font-medium text-gray-700">{{ $downtime->startedBy->name }}</p>
                            </div>

                            <!-- İşlem Butonları -->
                            @if(auth()->user()->role !== 'manager')
                                <div class="flex gap-2">
                                    <form method="POST" action="{{ route('downtime.complete', $downtime) }}" class="flex-1">
                                        @csrf
                                        <button type="submit" class="btn btn-success w-full text-sm py-2"
                                            onclick="return confirm('Duruşu bitirmek istediğinize emin misiniz?')">
                                            ✅ Duruşu Bitir
                                        </button>
                                    </form>
                                    <a href="{{ route('downtime.edit', $downtime) }}" class="btn btn-secondary text-sm py-2 px-4">
                                        ✏️
                                    </a>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Bugünkü Duruşlar -->
        <div class="card overflow-hidden">
            <h2 class="text-xl font-bold mb-4">📊 Bugünkü Duruşlar</h2>
            @if($todayDowntimes->count() > 0)
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Makine</th>
                                <th>Hata Kodu</th>
                                <th>Başlangıç</th>
                                <th>Bitiş</th>
                                <th>Süre (dk)</th>
                                <th>Durum</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($todayDowntimes as $downtime)
                                <tr>
                                    <td>{{ $downtime->machine->name }}</td>
                                    <td>
                                        @if($downtime->errorCode)
                                            <span class="badge 
                                                                @if(($downtime->errorCode->category ?? '') === 'Mekanik') badge-red
                                                                @elseif(($downtime->errorCode->category ?? '') === 'Elektrik') badge-yellow
                                                                @else badge-blue
                                                                @endif
                                                            ">
                                                {{ $downtime->errorCode->code }}
                                            </span>
                                        @else
                                            <span class="badge badge-gray">N/A</span>
                                        @endif
                                    </td>
                                    <td>{{ $downtime->started_at->format('H:i') }}</td>
                                    <td>{{ $downtime->ended_at ? $downtime->ended_at->format('H:i') : '-' }}</td>
                                    <td class="font-bold">{{ $downtime->duration_minutes ?? '-' }}</td>
                                    <td>
                                        @if($downtime->status === 'active')
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
            @else
                <p class="text-center text-gray-500 py-4">Bugün henüz duruş kaydı yok.</p>
            @endif
        </div>
    </div>

    <script>
        // Canlı süre sayacı - Her saniye güncellenir
        document.addEventListener('DOMContentLoaded', function () {
            const timers = document.querySelectorAll('.live-timer');

            if (timers.length === 0) return;

            function updateTimers() {
                const now = Math.floor(Date.now() / 1000);

                timers.forEach(timer => {
                    const startTimestamp = parseInt(timer.dataset.start);
                    const elapsedSeconds = now - startTimestamp;

                    // Saat, dakika, saniye hesapla
                    const hours = Math.floor(elapsedSeconds / 3600);
                    const minutes = Math.floor((elapsedSeconds % 3600) / 60);
                    const seconds = elapsedSeconds % 60;

                    // Format: HH:MM:SS
                    const formatted =
                        String(hours).padStart(2, '0') + ':' +
                        String(minutes).padStart(2, '0') + ':' +
                        String(seconds).padStart(2, '0');

                    timer.textContent = formatted;

                    // Renk kodlama
                    const totalMinutes = Math.floor(elapsedSeconds / 60);
                    if (totalMinutes > 240) {
                        timer.className = 'live-timer font-mono font-bold text-xl text-red-600';
                    } else if (totalMinutes > 120) {
                        timer.className = 'live-timer font-mono font-bold text-xl text-orange-600';
                    } else {
                        timer.className = 'live-timer font-mono font-bold text-xl text-yellow-600';
                    }
                });
            }

            // İlk güncelleme
            updateTimers();

            // Her saniye güncelle
            setInterval(updateTimers, 1000);
        });
    </script>
@endsection