@extends('layouts.app')

@section('title', 'Raporlar')

@section('content')
    <div class="space-y-6">
        <h1 class="text-3xl font-bold text-gray-900">📊 Raporlama Sistemi</h1>

        <!-- Grup 1: Genel Raporlar -->
        <div class="space-y-4">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-blue-100 rounded-lg">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                        </path>
                    </svg>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">Genel Duruş Raporları</h2>
                    <p class="text-sm text-gray-600">Günlük, aylık ve yıllık periyotlara göre tüm duruş kayıtlarını
                        görüntüleyin</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Günlük Rapor -->
                <div class="card hover:shadow-lg transition-shadow group">
                    <div class="flex items-center justify-center mb-4">
                        <div class="p-4 bg-blue-100 rounded-full group-hover:bg-blue-200 transition-colors">
                            <svg class="w-12 h-12 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                </path>
                            </svg>
                        </div>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 text-center mb-2">📅 Günlük Rapor</h3>
                    <p class="text-gray-600 text-center text-sm mb-4">Belirli bir güne ait duruş kayıtları ve istatistikler
                    </p>
                    <ul class="text-sm text-gray-600 space-y-1 mb-4">
                        <li class="flex items-center gap-2">
                            <span class="text-green-500">✓</span> Makine bazlı grafik
                        </li>
                        <li class="flex items-center gap-2">
                            <span class="text-green-500">✓</span> Hata kodu dağılımı
                        </li>
                        <li class="flex items-center gap-2">
                            <span class="text-green-500">✓</span> Detaylı kayıt listesi
                        </li>
                    </ul>
                    <a href="{{ route('reports.daily') }}" class="btn btn-primary w-full">
                        Görüntüle →
                    </a>
                </div>

                <!-- Aylık Rapor -->
                <div class="card hover:shadow-lg transition-shadow group">
                    <div class="flex items-center justify-center mb-4">
                        <div class="p-4 bg-green-100 rounded-full group-hover:bg-green-200 transition-colors">
                            <svg class="w-12 h-12 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                                </path>
                            </svg>
                        </div>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 text-center mb-2">📊 Aylık Rapor</h3>
                    <p class="text-gray-600 text-center text-sm mb-4">Aylık duruş trendleri ve karşılaştırmalar</p>
                    <ul class="text-sm text-gray-600 space-y-1 mb-4">
                        <li class="flex items-center gap-2">
                            <span class="text-green-500">✓</span> 30 günlük trend grafiği
                        </li>
                        <li class="flex items-center gap-2">
                            <span class="text-green-500">✓</span> Kategori analizi
                        </li>
                        <li class="flex items-center gap-2">
                            <span class="text-green-500">✓</span> Makine performansı
                        </li>
                    </ul>
                    <a href="{{ route('reports.monthly') }}" class="btn btn-success w-full">
                        Görüntüle →
                    </a>
                </div>

                <!-- Yıllık Rapor -->
                <div class="card hover:shadow-lg transition-shadow group">
                    <div class="flex items-center justify-center mb-4">
                        <div class="p-4 bg-yellow-100 rounded-full group-hover:bg-yellow-200 transition-colors">
                            <svg class="w-12 h-12 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                                </path>
                            </svg>
                        </div>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 text-center mb-2">📈 Yıllık Rapor</h3>
                    <p class="text-gray-600 text-center text-sm mb-4">Yıllık performans analizi ve özet bilgiler</p>
                    <ul class="text-sm text-gray-600 space-y-1 mb-4">
                        <li class="flex items-center gap-2">
                            <span class="text-green-500">✓</span> 12 aylık karşılaştırma
                        </li>
                        <li class="flex items-center gap-2">
                            <span class="text-green-500">✓</span> Top 10 problemli makine
                        </li>
                        <li class="flex items-center gap-2">
                            <span class="text-green-500">✓</span> Hata kodu dağılımı
                        </li>
                    </ul>
                    <a href="{{ route('reports.yearly') }}" class="btn btn-warning w-full">
                        Görüntüle →
                    </a>
                </div>
            </div>
        </div>

        <hr class="border-t-2 border-gray-200">

        <!-- Grup 2: Hata Kodu Bazlı Analiz -->
        <div class="space-y-4">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-red-100 rounded-lg">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                        </path>
                    </svg>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">Hata Kodu Bazlı Analiz</h2>
                    <p class="text-sm text-gray-600">Belirli bir hata koduna göre detaylı duruş analizi ve hangi günlerde
                        oluştuğunu görme</p>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-6">
                <!-- Hata Kodu Analizi -->
                <div
                    class="card hover:shadow-xl transition-shadow bg-gradient-to-br from-red-50 to-orange-50 border-2 border-red-200">
                    <div class="flex items-start gap-6">
                        <div class="p-6 bg-red-100 rounded-2xl">
                            <svg class="w-16 h-16 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"></path>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <h3 class="text-2xl font-bold text-gray-900 mb-3">🔍 Hata Kodu Detay Analizi</h3>
                            <p class="text-gray-700 mb-4 leading-relaxed">
                                Belirli bir hata kodunu (örn: <span class="font-semibold text-red-700">Elektrik
                                    Arızası</span>,
                                <span class="font-semibold text-red-700">Malzeme Bekleme</span>) seçerek,
                                bu hatanın <span class="font-semibold">hangi günlerde</span>,
                                <span class="font-semibold">hangi makinelerde</span> ve
                                <span class="font-semibold">ne kadar süre</span> duruş oluşturduğunu detaylı olarak
                                görüntüleyin.
                            </p>
                            <ul class="grid grid-cols-2 gap-3 mb-6">
                                <li class="flex items-center gap-2 text-sm text-gray-700">
                                    <span class="text-red-500 text-lg">✓</span> Hata koduna göre filtreleme
                                </li>
                                <li class="flex items-center gap-2 text-sm text-gray-700">
                                    <span class="text-red-500 text-lg">✓</span> Tarih aralığı seçimi
                                </li>
                                <li class="flex items-center gap-2 text-sm text-gray-700">
                                    <span class="text-red-500 text-lg">✓</span> Günlük breakdown tablosu
                                </li>
                                <li class="flex items-center gap-2 text-sm text-gray-700">
                                    <span class="text-red-500 text-lg">✓</span> Makine dağılım grafiği
                                </li>
                                <li class="flex items-center gap-2 text-sm text-gray-700">
                                    <span class="text-red-500 text-lg">✓</span> Toplam ve ortalama istatistikler
                                </li>
                                <li class="flex items-center gap-2 text-sm text-gray-700">
                                    <span class="text-red-500 text-lg">✓</span> Günlük trend grafiği
                                </li>
                            </ul>
                            <a href="{{ route('reports.error-analysis') }}"
                                class="btn btn-danger text-lg py-3 px-8 inline-block">
                                🔍 Hata Kodu Analiz Et →
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bilgilendirme -->
        <div class="card bg-blue-50 border-blue-200">
            <div class="flex items-start gap-4">
                <div class="p-2 bg-blue-200 rounded-lg">
                    <svg class="w-6 h-6 text-blue-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="flex-1">
                    <h4 class="font-bold text-blue-900 mb-1">💡 İpucu</h4>
                    <p class="text-sm text-blue-700">
                        <strong>Genel Raporlar</strong> tüm duruşları zaman bazında gösterirken,
                        <strong>Hata Kodu Analizi</strong> belirli bir arıza tipinin zaman içindeki dağılımını görmenizi
                        sağlar.
                        Örneğin "Elektrik kesintisi" hatası son 30 günde hangi günlerde oluşmuş, detaylı olarak
                        inceleyebilirsiniz.
                    </p>
                </div>
            </div>
        </div>
    </div>
@endsection