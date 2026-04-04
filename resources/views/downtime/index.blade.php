@extends('layouts.app')

@section('title', 'Duruş Kayıtları')

@section('content')
    <div class="max-w-7xl mx-auto space-y-6">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Duruş Kayıtları</h1>
                <p class="text-gray-600 mt-1">Ocak duruş kayıtlarını görüntüle ve yönet</p>
            </div>
            <a href="{{ route('downtime.create') }}" class="btn btn-accent">
                🚨 Yeni Duruş Başlat
            </a>
        </div>

        <!-- Tabs -->
        <div class="border-b border-gray-200">
            <nav class="-mb-px flex space-x-8">
                <a href="?status=active"
                    class="border-transparent {{ $status === 'active' ? 'border-accent-500 text-accent-600 border-b-2' : 'text-gray-500 hover:text-gray-700 hover:border-gray-300' }} whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                    🔴 Aktif Duruşlar ({{ $activeCount }})
                </a>
                <a href="?status=completed"
                    class="border-transparent {{ $status === 'completed' ? 'border-accent-500 text-accent-600 border-b-2' : 'text-gray-500 hover:text-gray-700 hover:border-gray-300' }} whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                    ✅ Tamamlanan Duruşlar ({{ $completedCount }})
                </a>
            </nav>
        </div>

        @if(request('status') === 'completed')
            <!-- Tamamlanan Duruşlar -->
            <div class="card">
                <div class="mb-4 p-4 bg-gray-50 rounded-lg">
                    <p class="text-sm text-gray-700"><strong>Toplam:</strong>
                        {{ \App\Models\DowntimeRecord::where('status', 'completed')->count() }} kayıt</p>
                    <p class="text-sm text-gray-700"><strong>Toplam Süre:</strong>
                        {{ number_format(\App\Models\DowntimeRecord::where('status', 'completed')->sum('duration_minutes') / 60, 1) }}
                        saat</p>
                </div>

                <div class="overflow-x-auto">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Tarih</th>
                                <th>Ocak</th>
                                <th>Hata Kodu</th>
                                <th>Süre</th>
                                <th>Notlar</th>
                                <th>İşlem</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($downtimes as $downtime)
                                <tr>
                                    <td>
                                        <p class="font-medium">{{ $downtime->started_at->format('d.m.Y') }}</p>
                                        <p class="text-xs text-gray-500">{{ $downtime->started_at->format('H:i') }} -
                                            {{ $downtime->ended_at->format('H:i') }}</p>
                                    </td>
                                    <td>
                                        <span class="badge badge-primary">{{ $downtime->machine->code }}</span>
                                    </td>
                                    <td>
                                        @if($downtime->errorCode)
                                            <p class="font-medium text-sm">{{ $downtime->errorCode->code }}</p>
                                            <p class="text-xs text-gray-600">{{ $downtime->errorCode->name }}</p>
                                            <span class="badge badge-gray text-xs mt-1">{{ $downtime->errorCode->category ?? 'N/A' }}</span>
                                        @else
                                            <span class="badge badge-gray text-xs">N/A</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span
                                            class="font-bold text-lg {{ $downtime->duration_minutes > 300 ? 'text-red-600' : 'text-gray-900' }}">
                                            {{ $downtime->duration_minutes }}
                                        </span>
                                        <span class="text-xs text-gray-500">dk</span>
                                        <p class="text-xs text-gray-500">({{ number_format($downtime->duration_minutes / 60, 1) }}
                                            saat)</p>
                                    </td>
                                    <td class="text-sm text-gray-600 max-w-xs">
                                        {{ Str::limit($downtime->notes, 80) }}
                                    </td>
                                    <td>
                                        <div class="flex gap-2">
                                            <a href="{{ route('downtime.show', $downtime) }}" class="btn btn-secondary btn-sm">
                                                👁️ Detay
                                            </a>
                                            @if(in_array(auth()->user()->role, ['admin', 'manager']))
                                            <form method="POST" action="{{ route('downtime.destroy', $downtime) }}" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Bu kaydı kalıcı olarak silmek istediğinize emin misiniz?')">
                                                    🗑️
                                                </button>
                                            </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-8 text-gray-500">
                                        Henüz tamamlanan duruş kaydı yok
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                <div class="mt-4">
                    {{ $downtimes->links() }}
                </div>
            </div>
        @else
            <!-- Aktif Duruşlar -->
            @if($downtimes->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @foreach($downtimes as $downtime)
                        <div class="card border-l-4 border-red-500 bg-red-50">
                            <div class="flex justify-between items-start mb-4">
                                <div>
                                    <h3 class="text-xl font-bold text-gray-900">{{ $downtime->machine->code }} -
                                        {{ $downtime->machine->name }}</h3>
                                    <p class="text-sm text-gray-600">{{ $downtime->machine->location }}</p>
                                </div>
                                <span class="badge badge-red text-lg px-3 py-1">AKTİF</span>
                            </div>

                            <div class="space-y-3">
                                <div class="flex items-center justify-between p-3 bg-white rounded-lg">
                                    <div>
                                        <p class="text-sm text-gray-600">Hata Kodu</p>
                                        @if($downtime->errorCode)
                                            <p class="font-bold text-gray-900">{{ $downtime->errorCode->code }} -
                                                {{ $downtime->errorCode->name }}</p>
                                            <span class="badge badge-blue text-xs mt-1">{{ $downtime->errorCode->category ?? 'N/A' }}</span>
                                        @else
                                            <p class="font-bold text-gray-500">N/A</p>
                                        @endif
                                    </div>
                                </div>

                                <div class="flex items-center justify-between p-3 bg-white rounded-lg">
                                    <div>
                                        <p class="text-sm text-gray-600">Başlangıç</p>
                                        <p class="font-medium">{{ $downtime->started_at->format('d.m.Y H:i') }}</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-sm text-gray-600">Geçen Süre</p>
                                        <div class="live-timer font-mono font-bold text-3xl" 
                                             data-start="{{ $downtime->started_at->timestamp }}"
                                             id="timer-{{ $downtime->id }}">
                                            00:00:00
                                        </div>
                                        <p class="text-xs text-gray-500 mt-1" id="timer-total-{{ $downtime->id }}">
                                            (0 dakika toplam)
                                        </p>
                                    </div>
                                </div>

                                @if($downtime->notes)
                                    <div class="p-3 bg-white rounded-lg">
                                        <p class="text-sm text-gray-600">Notlar</p>
                                        <p class="text-sm text-gray-800">{{ $downtime->notes }}</p>
                                    </div>
                                @endif

                                <div class="flex space-x-2">
                                    <form method="POST" action="{{ route('downtime.complete', $downtime) }}" class="flex-1">
                                        @csrf
                                        <button type="submit" class="btn btn-success w-full"
                                            onclick="return confirm('Duruşu bitirmek istediğinize emin misiniz?')">
                                            ✅ Duruşu Bitir
                                        </button>
                                    </form>
                                    <a href="{{ route('downtime.edit', $downtime) }}" class="btn btn-secondary">
                                        ✏️ Düzenle
                                    </a>
                                    @if(in_array(auth()->user()->role, ['admin', 'manager']))
                                    <form method="POST" action="{{ route('downtime.destroy', $downtime) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger" onclick="return confirm('Bu kaydı kalıcı olarak silmek istediğinize emin misiniz?')">
                                            🗑️
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <div class="mt-4">
                    {{ $downtimes->links() }}
                </div>
            @else
                <div class="card text-center py-16 bg-green-50 border-green-200">
                    <div class="text-6xl mb-4">✅</div>
                    <h2 class="text-2xl font-bold text-gray-900 mb-2">Tebrikler!</h2>
                    <p class="text-lg text-gray-600 mb-4">Şu anda hiç aktif duruş bulunmuyor.</p>
                    <p class="text-gray-500">Tüm ocaklar normal çalışıyor.</p>
                </div>
            @endif
        @endif
    </div>

<script>
// Canlı süre sayacı - Her saniye güncellenir
document.addEventListener('DOMContentLoaded', function() {
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
                timer.className = 'live-timer font-mono font-bold text-3xl text-red-600';
            } else if (totalMinutes > 120) {
                timer.className = 'live-timer font-mono font-bold text-3xl text-orange-600';
            } else {
                timer.className = 'live-timer font-mono font-bold text-3xl text-yellow-600';
            }
            
            // Toplam dakika güncelle
            const totalElement = document.getElementById('timer-total-' + timer.id.replace('timer-', ''));
            if (totalElement) {
                totalElement.textContent = `(${totalMinutes} dakika toplam)`;
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