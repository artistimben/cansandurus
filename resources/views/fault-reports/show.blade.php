@extends('layouts.app')

@section('title', 'Arıza Detayı')

@section('content')
    <div class="max-w-3xl mx-auto space-y-6">

        <div class="flex items-center gap-4">
            <a href="{{ route('fault-reports.index') }}" class="text-gray-500 hover:text-gray-700">← Geri</a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">🔧 Arıza Detayı</h1>
                <p class="text-gray-500 text-sm">#{{ $faultReport->id }}</p>
            </div>
        </div>

        {{-- Durum Başlık Kartı --}}
        <div
            class="card border-l-4 {{ $faultReport->isResolved() ? 'border-green-500 bg-green-50' : ($faultReport->priority === 'critical' ? 'border-red-500 bg-red-50' : 'border-orange-400 bg-orange-50') }}">
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start gap-4">
                <div>
                    <h2 class="text-xl font-bold text-gray-900">{{ $faultReport->title }}</h2>
                    <div class="flex flex-wrap gap-2 mt-2">
                        <span
                            class="badge {{ $faultReport->getPriorityColor() }}">{{ $faultReport->getPriorityLabel() }}</span>
                        <span class="badge {{ $faultReport->getStatusColor() }}">{{ $faultReport->getStatusLabel() }}</span>
                        @if($faultReport->production_continued)
                            <span class="badge badge-green">✅ Üretim Devam Etti</span>
                        @else
                            <span class="badge badge-orange">⚠️ Üretim Etkilendi</span>
                        @endif
                    </div>
                </div>
                <div class="flex gap-2 flex-shrink-0">
                    @if(!$faultReport->isResolved())
                        <a href="{{ route('fault-reports.edit', $faultReport) }}" class="btn btn-primary btn-sm">
                            ✏️ Düzenle
                        </a>
                    @endif
                    @if(in_array(auth()->user()->role, ['admin', 'manager']) && $faultReport->isResolved())
                        <a href="{{ route('fault-reports.edit', $faultReport) }}" class="btn btn-secondary btn-sm">
                            ✏️ Düzenle
                        </a>
                    @endif
                </div>
            </div>
        </div>

        {{-- Bilgi Grid --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="card space-y-3">
                <h3 class="font-semibold text-gray-700 border-b pb-2">📋 Temel Bilgiler</h3>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-500">Makine:</span>
                        <span class="font-medium">{{ $faultReport->machine->code ?? 'N/A' }} –
                            {{ $faultReport->machine->name ?? '' }}</span>
                    </div>
                    @if($faultReport->machine->location)
                        <div class="flex justify-between">
                            <span class="text-gray-500">Konum:</span>
                            <span>{{ $faultReport->machine->location }}</span>
                        </div>
                    @endif
                    @if($faultReport->errorCode)
                        <div class="flex justify-between">
                            <span class="text-gray-500">Hata Kodu:</span>
                            <span class="font-medium">{{ $faultReport->errorCode->code }} –
                                {{ $faultReport->errorCode->name }}</span>
                        </div>
                    @endif
                    <div class="flex justify-between">
                        <span class="text-gray-500">Bildiren:</span>
                        <span>{{ $faultReport->reportedBy->name ?? 'N/A' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Bildirim Zamanı:</span>
                        <span>{{ $faultReport->reported_at->format('d.m.Y H:i') }}</span>
                    </div>
                </div>
            </div>

            <div class="card space-y-3">
                <h3 class="font-semibold text-gray-700 border-b pb-2">✅ Çözüm Bilgileri</h3>
                @if($faultReport->isResolved())
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-500">Çözen:</span>
                            <span>{{ $faultReport->resolvedBy->name ?? 'N/A' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Çözüm Zamanı:</span>
                            <span>{{ $faultReport->resolved_at->format('d.m.Y H:i') }}</span>
                        </div>
                        @if($faultReport->getResolutionMinutes() !== null)
                            <div class="flex justify-between">
                                <span class="text-gray-500">Çözüm Süresi:</span>
                                <span class="font-medium text-green-700">{{ $faultReport->getResolutionMinutes() }} dakika</span>
                            </div>
                        @endif
                    </div>
                @else
                    <div class="text-center py-4 text-gray-400">
                        <p class="text-sm">Henüz çözülmedi</p>
                    </div>

                    {{-- Hızlı Çözüm Butonu --}}
                    <div class="mt-2">
                        <form method="POST" action="{{ route('fault-reports.resolve', $faultReport) }}">
                            @csrf
                            <div class="mb-2">
                                <textarea name="action_taken" rows="2" class="form-input text-sm"
                                    placeholder="Alınan önlem / yapılan işlem (opsiyonel)"></textarea>
                            </div>
                            <button type="submit" class="btn btn-success w-full btn-sm"
                                onclick="return confirm('Arızayı çözüldü olarak işaretlemek istiyor musunuz?')">
                                ✅ Çözüldü Olarak İşaretle
                            </button>
                        </form>
                    </div>
                @endif
            </div>
        </div>

        {{-- Açıklama --}}
        @if($faultReport->description)
            <div class="card">
                <h3 class="font-semibold text-gray-700 mb-3">📝 Arıza Açıklaması</h3>
                <p class="text-gray-800 text-sm leading-relaxed whitespace-pre-wrap">{{ $faultReport->description }}</p>
            </div>
        @endif

        {{-- Alınan Önlem --}}
        @if($faultReport->action_taken)
            <div class="card bg-green-50 border-green-200">
                <h3 class="font-semibold text-green-800 mb-3">🔧 Alınan Önlem / Yapılan İşlem</h3>
                <p class="text-green-900 text-sm leading-relaxed whitespace-pre-wrap">{{ $faultReport->action_taken }}</p>
            </div>
        @endif

        {{-- Alt işlemler --}}
        <div class="flex gap-3">
            <a href="{{ route('fault-reports.index') }}" class="btn btn-secondary">← Listeye Dön</a>
            @if(in_array(auth()->user()->role, ['admin', 'manager']))
                <form method="POST" action="{{ route('fault-reports.destroy', $faultReport) }}" class="ml-auto">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-sm"
                        onclick="return confirm('Bu arıza kaydını kalıcı olarak silmek istediğinize emin misiniz?')">
                        🗑️ Sil
                    </button>
                </form>
            @endif
        </div>

    </div>
@endsection