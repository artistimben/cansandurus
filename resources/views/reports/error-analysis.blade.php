@extends('layouts.app')

@section('title', 'Hata Kodu Analizi')

@section('content')
    <div class="max-w-7xl mx-auto space-y-6">
        <!-- Header -->
        <div class="flex justify-between items-center">
            <div>
                <a href="{{ route('reports.index') }}"
                    class="text-accent-600 hover:text-accent-700 font-medium mb-2 inline-block">
                    ← Raporlara Dön
                </a>
                <h1 class="text-3xl font-bold text-gray-900">🔍 Hata Kodu Analizi</h1>
                <p class="text-gray-600 mt-1">Belirli bir hata koduna göre detaylı duruş analizi</p>
            </div>
        </div>

        <!-- Filtre Formu -->
        <div class="card">
            <h2 class="text-lg font-bold text-gray-900 mb-4">Filtreler</h2>
            <form method="GET" action="{{ route('reports.error-analysis') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <!-- Hata Kodu Seçimi -->
                <div class="col-span-2">
                    <label for="error_code_id" class="label">Hata Kodu *</label>
                    <select name="error_code_id" id="error_code_id" class="input" required>
                        <option value="">Hata Kodu Seçin...</option>
                        @foreach($errorCodes as $errorCode)
                            <option value="{{ $errorCode->id }}" {{ (isset($selectedErrorCode) && $selectedErrorCode->id == $errorCode->id) ? 'selected' : '' }}>
                                {{ $errorCode->code }} - {{ $errorCode->name }} ({{ $errorCode->category }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Başlangıç Tarihi -->
                <div>
                    <label for="start_date" class="label">Başlangıç</label>
                    <input type="date" id="start_date" name="start_date" class="input"
                        value="{{ isset($selectedStartDate) ? $selectedStartDate->format('Y-m-d') : now()->startOfMonth()->format('Y-m-d') }}">
                </div>

                <!-- Bitiş Tarihi -->
                <div>
                    <label for="end_date" class="label">Bitiş</label>
                    <input type="date" id="end_date" name="end_date" class="input"
                        value="{{ isset($selectedEndDate) ? $selectedEndDate->format('Y-m-d') : now()->format('Y-m-d') }}">
                </div>

                <!-- Makine Filtresi (Opsiyonel) -->
                <div class="col-span-2">
                    <label for="machine_id" class="label">Makine (Opsiyonel)</label>
                    <select name="machine_id" id="machine_id" class="input">
                        <option value="">Tüm Makineler</option>
                        @foreach($machines as $machine)
                            <option value="{{ $machine->id }}" {{ (request('machine_id') == $machine->id) ? 'selected' : '' }}>
                                {{ $machine->code }} - {{ $machine->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Submit Butonu -->
                <div class="col-span-2 flex items-end">
                    <button type="submit" class="btn btn-accent w-full">
                        🔍 Analiz Et
                    </button>
                </div>
            </form>
        </div>

        @if(isset($selectedErrorCode))
            <!-- Seçili Hata Kodu Bilgisi -->
            <div class="card bg-gradient-to-br from-red-50 to-red-100 border-red-200">
                <div class="flex items-center gap-4">
                    <div class="p-4 bg-red-200 rounded-full">
                        <svg class="w-8 h-8 text-red-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                            </path>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-xl font-bold text-red-900">{{ $selectedErrorCode->code }}:
                            {{ $selectedErrorCode->name }}</h3>
                        <div class="flex gap-4 mt-1">
                            <span class="badge badge-gray">{{ $selectedErrorCode->category }}</span>
                            <span class="badge 
                                    @if($selectedErrorCode->severity === 'critical') badge-red
                                    @elseif($selectedErrorCode->severity === 'high') badge-orange
                                    @elseif($selectedErrorCode->severity === 'medium') badge-yellow
                                    @else badge-green
                                    @endif">
                                {{ ucfirst($selectedErrorCode->severity) }}
                            </span>
                            <span class="text-sm text-red-700">📅 {{ $selectedStartDate->format('d.m.Y') }} -
                                {{ $selectedEndDate->format('d.m.Y') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- İstatistik Kartları -->
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <!-- Toplam Duruş -->
                <div class="card bg-gradient-to-br from-primary-50 to-primary-100 border-primary-200">
                    <p class="text-sm font-medium text-primary-600">Toplam Duruş</p>
                    <p class="text-3xl font-bold text-primary-900 mt-2">{{ $stats['total_count'] }}</p>
                    <p class="text-xs text-primary-600 mt-1">kayıt</p>
                </div>

                <!-- Toplam Süre -->
                <div class="card bg-gradient-to-br from-yellow-50 to-yellow-100 border-yellow-200">
                    <p class="text-sm font-medium text-yellow-700">Toplam Süre</p>
                    <p class="text-2xl font-bold text-yellow-900 mt-2">{{ number_format($stats['total_duration']) }} dk</p>
                    <p class="text-xs text-yellow-700 mt-1">({{ number_format($stats['total_duration'] / 60, 1) }} saat)</p>
                </div>

                <!-- Ortalama Süre -->
                <div class="card bg-gradient-to-br from-blue-50 to-blue-100 border-blue-200">
                    <p class="text-sm font-medium text-blue-600">Ort. Süre</p>
                    <p class="text-3xl font-bold text-blue-900 mt-2">{{ number_format($stats['avg_duration'], 0) }}</p>
                    <p class="text-xs text-blue-600 mt-1">dakika</p>
                </div>

                <!-- Etkilenen Makineler -->
                <div class="card bg-gradient-to-br from-green-50 to-green-100 border-green-200">
                    <p class="text-sm font-medium text-green-600">Etkilenen Makine</p>
                    <p class="text-3xl font-bold text-green-900 mt-2">{{ $stats['affected_machines'] }}</p>
                    <p class="text-xs text-green-600 mt-1">farklı ocak</p>
                </div>

                <!-- Duruşlu Gün -->
                <div class="card bg-gradient-to-br from-red-50 to-red-100 border-red-200">
                    <p class="text-sm font-medium text-red-600">Duruşlu Gün</p>
                    <p class="text-3xl font-bold text-red-900 mt-2">{{ $stats['days_with_downtime'] }}</p>
                    <p class="text-xs text-red-600 mt-1">/ {{ $selectedStartDate->diffInDays($selectedEndDate) + 1 }} gün</p>
                </div>
            </div>

            <!-- Günlük Trend Grafiği -->
            @if(count($dailyBreakdown) > 0)
                <div class="card">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">📈 Günlük Trend</h3>
                    <div class="h-80">
                        <canvas id="dailyTrendChart"></canvas>
                    </div>
                </div>
            @endif

            <!-- Günlük Detay Tablosu -->
            <div class="card">
                <h2 class="text-xl font-bold text-gray-900 mb-4">📅 Günlük Detay</h2>
                <div class="overflow-x-auto">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Tarih</th>
                                <th>Duruş Sayısı</th>
                                <th>Toplam Süre</th>
                                <th>Aktif</th>
                                <th>Etkilenen Makineler</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($dailyBreakdown as $day)
                                @if($day['count'] > 0)
                                    <tr class="{{ $day['active_count'] > 0 ? 'bg-red-50' : '' }}">
                                        <td class="font-medium">{{ $day['date_formatted'] }}</td>
                                        <td class="text-center">
                                            <span class="badge badge-primary">{{ $day['count'] }}</span>
                                        </td>
                                        <td>
                                            <p class="font-bold">{{ number_format($day['duration']) }} dk</p>
                                            <p class="text-xs text-gray-500">({{ number_format($day['duration'] / 60, 1) }} saat)</p>
                                        </td>
                                        <td class="text-center">
                                            @if($day['active_count'] > 0)
                                                <span class="badge badge-red">{{ $day['active_count'] }} aktif</span>
                                            @else
                                                <span class="text-gray-400">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="flex gap-1 flex-wrap">
                                                @foreach($day['machines'] as $machine)
                                                    <span class="badge badge-gray text-xs">{{ $machine->code }}</span>
                                                @endforeach
                                            </div>
                                        </td>
                                    </tr>
                                @endif
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-gray-500 py-8">
                                        <div class="text-4xl mb-2">🎉</div>
                                        <p>Seçilen tarih aralığında bu hata kodu için duruş kaydı bulunamadı!</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Makine Bazında Dağılım -->
            @if($byMachine->count() > 0)
                <div class="card">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">🏭 Makine Bazında Dağılım</h2>
                    <div class="overflow-x-auto">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Makine</th>
                                    <th>Duruş Sayısı</th>
                                    <th>Toplam Süre</th>
                                    <th>Ort. Süre</th>
                                    <th>Yüzde</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($byMachine as $item)
                                    <tr>
                                        <td>
                                            <div>
                                                <span class="badge badge-primary">{{ $item['machine']->code }}</span>
                                                <span class="font-medium">{{ $item['machine']->name }}</span>
                                            </div>
                                            <p class="text-xs text-gray-500">{{ $item['machine']->location }}</p>
                                        </td>
                                        <td class="text-center font-bold">{{ $item['count'] }}</td>
                                        <td>
                                            <p class="font-bold">{{ number_format($item['duration']) }} dk</p>
                                            <p class="text-xs text-gray-500">({{ number_format($item['duration'] / 60, 1) }} saat)</p>
                                        </td>
                                        <td class="text-gray-700">{{ number_format($item['duration'] / $item['count'], 0) }} dk</td>
                                        <td>
                                            <div class="flex items-center gap-2">
                                                <div class="flex-1 bg-gray-200 rounded-full h-2">
                                                    <div class="bg-accent-600 h-2 rounded-full"
                                                        style="width: {{ $stats['total_duration'] > 0 ? ($item['duration'] / $stats['total_duration']) * 100 : 0 }}%">
                                                    </div>
                                                </div>
                                                <span class="text-sm font-medium">
                                                    {{ $stats['total_duration'] > 0 ? number_format(($item['duration'] / $stats['total_duration']) * 100, 1) : 0 }}%
                                                </span>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        @else
            <!-- Başlangıç Mesajı -->
            <div class="card text-center py-12">
                <div class="text-6xl mb-4">🔍</div>
                <h3 class="text-2xl font-bold text-gray-900 mb-2">Hata Kodu Analizi</h3>
                <p class="text-gray-600 max-w-md mx-auto">
                    Yukarıdaki form ile bir hata kodu seçin ve hangi günlerde bu hata kodundan dolayı duruş oluştuğunu görün.
                </p>
            </div>
        @endif
    </div>

    <!-- Chart.js Script -->
    @if(isset($selectedErrorCode) && count($dailyBreakdown) > 0)
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const dates = {!! json_encode(collect($dailyBreakdown)->pluck('date')->map(fn($d) => \Carbon\Carbon::parse($d)->format('d/m'))->values()) !!};
                const counts = {!! json_encode(collect($dailyBreakdown)->pluck('count')->values()) !!};
                const durations = {!! json_encode(collect($dailyBreakdown)->pluck('duration')->values()) !!};

                if (window.ChartHelpers) {
                    window.ChartHelpers.createLineChart(
                        'dailyTrendChart',
                        dates,
                        [
                            {
                                label: 'Duruş Süresi (dk)',
                                data: durations,
                                color: window.ChartHelpers.ChartColors.danger,
                                fill: true
                            },
                            {
                                label: 'Duruş Sayısı',
                                data: counts,
                                color: window.ChartHelpers.ChartColors.secondary,
                                fill: false
                            }
                        ]
                    );
                }
            });
        </script>
    @endif
@endsection