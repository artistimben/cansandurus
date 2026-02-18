@extends('layouts.app')

@section('title', 'Günlük Rapor')

@section('content')
    <div class="max-w-7xl mx-auto space-y-6">
        <!-- Header -->
        <div class="flex justify-between items-center">
            <div>
                <a href="{{ route('reports.index') }}"
                    class="text-accent-600 hover:text-accent-700 font-medium mb-2 inline-block">
                    ← Raporlara Dön
                </a>
                <h1 class="text-3xl font-bold text-gray-900">📊 Günlük Duruş Raporu</h1>
                <p class="text-gray-600 mt-1">Seçilen güne ait detaylı duruş analizi</p>
            </div>
        </div>

        <!-- Tarih Seçici -->
        <div class="card">
            <form method="GET" action="{{ route('reports.daily') }}" class="flex gap-4 items-end">
                <div class="flex-1">
                    <label for="date" class="label">Rapor Tarihi</label>
                    <input type="date" id="date" name="date" class="input" value="{{ $selectedDate->format('Y-m-d') }}"
                        required>
                </div>
                <button type="submit" class="btn btn-accent">
                    🔍 Görüntüle
                </button>
            </form>
        </div>

        <!-- İstatistik Kartları -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <!-- Toplam Duruş -->
            <div class="card bg-gradient-to-br from-primary-50 to-primary-100 border-primary-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-primary-600">Toplam Duruş</p>
                        <p class="text-3xl font-bold text-primary-900 mt-2">{{ $stats['total_count'] }}</p>
                        <p class="text-xs text-primary-600 mt-1">kayıt</p>
                    </div>
                    <div class="p-3 bg-primary-200 rounded-full">
                        <svg class="w-8 h-8 text-primary-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 00-2-2m0 0h6a2 2 0 002-2V9a2 2 0 00-2-2h-2a2 2 0 00-2 2v6a2 2 0 002 2zm10 0v-9a2 2 0 00-2-2h-2a2 2 0 00-2 2v9a2 2 0 002 2h2a2 2 0 002-2z">
                            </path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Toplam Süre -->
            <div class="card bg-gradient-to-br from-yellow-50 to-yellow-100 border-yellow-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-yellow-700">Toplam Süre</p>
                        <p class="text-3xl font-bold text-yellow-900 mt-2">{{ number_format($stats['total_duration']) }}</p>
                        <p class="text-xs text-yellow-700 mt-1">dakika
                            ({{ number_format($stats['total_duration'] / 60, 1) }} saat)</p>
                    </div>
                    <div class="p-3 bg-yellow-200 rounded-full">
                        <svg class="w-8 h-8 text-yellow-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Aktif Duruş -->
            <div class="card bg-gradient-to-br from-red-50 to-red-100 border-red-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-red-600">Aktif Duruş</p>
                        <p class="text-3xl font-bold text-red-900 mt-2">{{ $stats['active_count'] }}</p>
                        <p class="text-xs text-red-600 mt-1">devam ediyor</p>
                    </div>
                    <div class="p-3 bg-red-200 rounded-full">
                        <svg class="w-8 h-8 text-red-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                            </path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Tamamlanan -->
            <div class="card bg-gradient-to-br from-green-50 to-green-100 border-green-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-green-600">Tamamlanan</p>
                        <p class="text-3xl font-bold text-green-900 mt-2">{{ $stats['completed_count'] }}</p>
                        <p class="text-xs text-green-600 mt-1">kayıt</p>
                    </div>
                    <div class="p-3 bg-green-200 rounded-full">
                        <svg class="w-8 h-8 text-green-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Görsel Analiz - Grafikler -->
        @if($byMachine->count() > 0 || $byErrorCode->count() > 0)
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Makine Dağılımı - Pie Chart -->
                @if($byMachine->count() > 0)
                    <div class="card">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">📊 Makine Bazında Dağılım</h3>
                        <div class="h-64">
                            <canvas id="machineDistributionChart"></canvas>
                        </div>
                    </div>
                @endif

                <!-- Hata Kodu Sıralaması - Bar Chart -->
                @if($byErrorCode->count() > 0)
                    <div class="card">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">⚠️ En Sık Hata Kodları</h3>
                        <div class="h-64">
                            <canvas id="errorCodeChart"></canvas>
                        </div>
                    </div>
                @endif
            </div>
        @endif

        <!-- Makine Bazında Analiz -->
        @if($byMachine->count() > 0)
            <div class="card">
                <h2 class="text-xl font-bold text-gray-900 mb-4">🏭 Ocak Bazında Duruşlar</h2>
                <div class="overflow-x-auto">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Ocak Kodu</th>
                                <th>Ocak Adı</th>
                                <th>Set</th>
                                <th>Duruş Sayısı</th>
                                <th>Toplam Süre</th>
                                <th>Ort. Süre</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($byMachine as $item)
                                @if($item['machine'])
                                    <tr>
                                        <td><span class="badge badge-primary">{{ $item['machine']->code }}</span></td>
                                        <td class="font-medium">{{ $item['machine']->name }}</td>
                                        <td class="text-gray-600">{{ $item['machine']->location }}</td>
                                        <td class="text-center font-bold">{{ $item['count'] }}</td>
                                        <td>
                                            <p class="font-bold text-lg">{{ number_format($item['duration']) }} dk</p>
                                            <p class="text-xs text-gray-500">({{ number_format($item['duration'] / 60, 1) }} saat)</p>
                                        </td>
                                        <td class="text-gray-700">{{ number_format($item['duration'] / $item['count'], 0) }} dk</td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="bg-gray-50 font-bold">
                                <td colspan="3">TOPLAM</td>
                                <td class="text-center">{{ $byMachine->sum('count') }}</td>
                                <td>{{ number_format($byMachine->sum('duration')) }} dk</td>
                                <td>-</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        @endif

        <!-- Hata Kodu Bazında Analiz -->
        @if($byErrorCode->count() > 0)
            <div class="card">
                <h2 class="text-xl font-bold text-gray-900 mb-4">⚠️ Hata Kodu Bazında Duruşlar</h2>
                <div class="overflow-x-auto">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Hata Kodu</th>
                                <th>Hata Adı</th>
                                <th>Kategori</th>
                                <th>Duruş Sayısı</th>
                                <th>Toplam Süre</th>
                                <th>Oran</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($byErrorCode as $item)
                                @if($item['error_code'])
                                    <tr>
                                        <td><span class="badge badge-red">{{ $item['error_code']->code }}</span></td>
                                        <td class="font-medium">{{ $item['error_code']->name }}</td>
                                        <td><span class="badge badge-gray">{{ $item['error_code']->category ?? 'N/A' }}</span></td>
                                        <td class="text-center font-bold">{{ $item['count'] }}</td>
                                        <td>
                                            <p class="font-bold text-lg">{{ number_format($item['duration']) }} dk</p>
                                            <p class="text-xs text-gray-500">({{ number_format($item['duration'] / 60, 1) }} saat)</p>
                                        </td>
                                        <td>
                                            <div class="flex items-center gap-2">
                                                <div class="flex-1 bg-gray-200 rounded-full h-2">
                                                    <div class="bg-accent-600 h-2 rounded-full"
                                                        style="width: {{ $stats['total_duration'] > 0 ? ($item['duration'] / $stats['total_duration']) * 100 : 0 }}%">
                                                    </div>
                                                </div>
                                                <span
                                                    class="text-sm font-medium">{{ $stats['total_duration'] > 0 ? number_format(($item['duration'] / $stats['total_duration']) * 100, 1) : 0 }}%</span>
                                            </div>
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @else
            <div class="card text-center py-12 bg-green-50">
                <div class="text-6xl mb-4">🎉</div>
                <h3 class="text-2xl font-bold text-gray-900 mb-2">Harika Gün!</h3>
                <p class="text-gray-600">{{ $selectedDate->format('d.m.Y') }} tarihinde hiç duruş kaydı yok.</p>
            </div>
        @endif
    </div>

    <!-- Chart.js Scripts -->
    @if($byMachine->count() > 0 || $byErrorCode->count() > 0)
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                // Makine Dağılımı Pie Chart
                @if($byMachine->count() > 0)
                    const machineLabels = {!! json_encode($byMachine->pluck('machine')->map(fn($m) => $m->code)->values()) !!};
                    const machineData = {!! json_encode($byMachine->pluck('duration')->values()) !!};

                    if (window.ChartHelpers) {
                        window.ChartHelpers.createPieChart(
                            'machineDistributionChart',
                            machineLabels,
                            machineData
                        );
                    }
                @endif

                    // Hata Kodu Bar Chart
                    @if($byErrorCode->count() > 0)
                        const errorLabels = {!! json_encode($byErrorCode->take(10)->pluck('error_code')->map(fn($e) => $e->code)->values()) !!};
                        const errorCounts = {!! json_encode($byErrorCode->take(10)->pluck('count')->values()) !!};

                        if (window.ChartHelpers) {
                            window.ChartHelpers.createBarChart(
                                'errorCodeChart',
                                errorLabels,
                                [
                                    {
                                        label: 'Duruş Sayısı',
                                        data: errorCounts,
                                        color: window.ChartHelpers.ChartColors.danger
                                    }
                                ]
                            );
                        }
                    @endif
                                    });
        </script>
    @endif
@endsection