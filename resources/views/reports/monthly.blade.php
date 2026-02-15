@extends('layouts.app')

@section('title', 'Aylık Rapor')

@section('content')
    <div class="max-w-7xl mx-auto space-y-6">
        <!-- Header -->
        <div class="flex justify-between items-center">
            <div>
                <a href="{{ route('reports.index') }}"
                    class="text-accent-600 hover:text-accent-700 font-medium mb-2 inline-block">
                    ← Raporlara Dön
                </a>
                <h1 class="text-3xl font-bold text-gray-900">📊 Aylık Duruş Raporu</h1>
                <p class="text-gray-600 mt-1">Seçilen aylık detaylı duruş analizi</p>
            </div>
        </div>

        <!-- Ay Seçici -->
        <div class="card">
            <form method="GET" action="{{ route('reports.monthly') }}" class="flex gap-4 items-end">
                <div class="flex-1">
                    <label for="month" class="label">Rapor Ayı</label>
                    <input type="month" id="month" name="month" class="input" value="{{ $selectedMonth->format('Y-m') }}"
                        required>
                </div>
                <button type="submit" class="btn btn-accent">
                    🔍 Görüntüle
                </button>
            </form>
        </div>

        <!-- İstatistik Kartları -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="card bg-gradient-to-br from-primary-50 to-primary-100 border-primary-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-primary-600">Toplam Duruş</p>
                        <p class="text-4xl font-bold text-primary-900 mt-2">{{ $stats['total_count'] }}</p>
                        <p class="text-xs text-primary-600 mt-1">kayıt</p>
                    </div>
                    <div class="p-4 bg-primary-200 rounded-full">
                        <svg class="w-10 h-10 text-primary-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 00-2-2m0 0h6a2 2 0 002-2V9a2 2 0 00-2-2h-2a2 2 0 00-2 2v6a2 2 0 002 2zm10 0v-9a2 2 0 00-2-2h-2a2 2 0 00-2 2v9a2 2 0 002 2h2a2 2 0 002-2z">
                            </path>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="card bg-gradient-to-br from-yellow-50 to-yellow-100 border-yellow-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-yellow-700">Toplam Süre</p>
                        <p class="text-4xl font-bold text-yellow-900 mt-2">{{ number_format($stats['total_duration']) }}</p>
                        <p class="text-xs text-yellow-700 mt-1">dakika
                            ({{ number_format($stats['total_duration'] / 60, 1) }} saat)</p>
                    </div>
                    <div class="p-4 bg-yellow-200 rounded-full">
                        <svg class="w-10 h-10 text-yellow-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="card bg-gradient-to-br from-blue-50 to-blue-100 border-blue-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-blue-600">Ortalama Süre</p>
                        <p class="text-4xl font-bold text-blue-900 mt-2">{{ number_format($stats['avg_duration'], 1) }}</p>
                        <p class="text-xs text-blue-600 mt-1">dakika/duruş</p>
                    </div>
                    <div class="p-4 bg-blue-200 rounded-full">
                        <svg class="w-10 h-10 text-blue-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z">
                            </path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Ocak Bazında Analiz -->
        @if($byMachine->count() > 0)
            <div class="card">
                <h2 class="text-xl font-bold text-gray-900 mb-4">🏭 Ocak Bazında Özet</h2>
                <div class="overflow-x-auto">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Ocak</th>
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
                                        <td>
                                            <span class="badge badge-primary">{{ $item['machine']->code }}</span>
                                            <span class="ml-2 text-gray-700">{{ $item['machine']->name }}</span>
                                        </td>
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
                    </table>
                </div>
            </div>
        @endif

        <!-- Kategori Bazında Analiz -->
        @if($byCategory->count() > 0)
            <div class="card">
                <h2 class="text-xl font-bold text-gray-900 mb-4">📑 Kategori Bazında Özet</h2>
                <div class="overflow-x-auto">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Kategori</th>
                                <th>Duruş Sayısı</th>
                                <th>Toplam Süre</th>
                                <th>Oran</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($byCategory as $item)
                                <tr>
                                    <td><span class="badge badge-primary">{{ $item['category'] }}</span></td>
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
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>
@endsection