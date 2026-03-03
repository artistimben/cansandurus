@extends('layouts.app')

@section('title', 'Arıza Bildir')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    <div class="flex items-center gap-4">
        <a href="{{ route('fault-reports.index') }}" class="text-gray-500 hover:text-gray-700">← Geri</a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900">⚠️ Arıza Bildir</h1>
            <p class="text-gray-600 text-sm mt-0.5">Ocağı durdurmayan arızaları buraya kaydedin</p>
        </div>
    </div>

    {{-- Bilgi notu --}}
    <div class="p-4 bg-amber-50 border border-amber-200 rounded-lg">
        <div class="flex gap-3">
            <span class="text-2xl">ℹ️</span>
            <div>
                <p class="font-semibold text-amber-800">Bu form ne için?</p>
                <p class="text-sm text-amber-700 mt-1">
                    Fabrikada arıza oluştu ancak ocak durmadıysa bu formu kullanın.
                    Bu kayıtlar <strong>duruş istatistiklerine dahil edilmez</strong>,
                    yalnızca arıza takibi amacıyla tutulur.
                </p>
            </div>
        </div>
    </div>

    <form method="POST" action="{{ route('fault-reports.store') }}" class="space-y-6">
        @csrf

        <div class="card space-y-5">

            {{-- Makine --}}
            <div>
                <label for="machine_id" class="form-label">Makine / Ocak <span class="text-red-500">*</span></label>
                <select id="machine_id" name="machine_id" class="form-select" required>
                    <option value="">— Makine Seçin —</option>
                    @foreach($machines as $machine)
                        <option value="{{ $machine->id }}" {{ old('machine_id') == $machine->id ? 'selected' : '' }}>
                            {{ $machine->code }} – {{ $machine->name }}
                            @if($machine->location) ({{ $machine->location }}) @endif
                        </option>
                    @endforeach
                </select>
                @error('machine_id') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Arıza Başlığı --}}
            <div>
                <label for="title" class="form-label">Arıza Başlığı <span class="text-red-500">*</span></label>
                <input type="text" id="title" name="title"
                    value="{{ old('title') }}"
                    placeholder="Kısa ve açıklayıcı bir başlık girin"
                    class="form-input" required maxlength="200">
                @error('title') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Hata Kodu (opsiyonel) --}}
            <div>
                <label for="error_code_id" class="form-label">Hata Kodu <span class="text-gray-400 text-xs">(opsiyonel)</span></label>
                <select id="error_code_id" name="error_code_id" class="form-select">
                    <option value="">— Hata kodu yoksa boş bırakın —</option>
                    @foreach($errorsByCategory as $category => $codes)
                        <optgroup label="{{ $category }}">
                            @foreach($codes as $code)
                                <option value="{{ $code->id }}" {{ old('error_code_id') == $code->id ? 'selected' : '' }}>
                                    {{ $code->code }} – {{ $code->name }}
                                </option>
                            @endforeach
                        </optgroup>
                    @endforeach
                </select>
            </div>

            {{-- Öncelik --}}
            <div>
                <label class="form-label">Öncelik Seviyesi <span class="text-red-500">*</span></label>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mt-2">
                    @foreach(['low' => ['label'=>'Düşük','color'=>'border-gray-300 bg-gray-50 text-gray-700','icon'=>'🟢'],
                               'medium' => ['label'=>'Orta','color'=>'border-blue-300 bg-blue-50 text-blue-700','icon'=>'🔵'],
                               'high' => ['label'=>'Yüksek','color'=>'border-orange-300 bg-orange-50 text-orange-700','icon'=>'🟠'],
                               'critical' => ['label'=>'Kritik','color'=>'border-red-300 bg-red-50 text-red-700','icon'=>'🔴']] as $val => $info)
                        <label class="flex flex-col items-center p-3 border-2 rounded-lg cursor-pointer hover:opacity-80 transition
                            {{ old('priority', 'medium') === $val ? 'ring-2 ring-offset-1 ring-primary-500 ' . $info['color'] : $info['color'] }}">
                            <input type="radio" name="priority" value="{{ $val }}"
                                {{ old('priority', 'medium') === $val ? 'checked' : '' }}
                                class="sr-only">
                            <span class="text-2xl">{{ $info['icon'] }}</span>
                            <span class="font-medium text-sm mt-1">{{ $info['label'] }}</span>
                        </label>
                    @endforeach
                </div>
                @error('priority') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Üretim devam etti mi? --}}
            <div class="p-4 bg-gray-50 rounded-lg">
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" name="production_continued" value="1"
                        {{ old('production_continued', '1') ? 'checked' : '' }}
                        class="w-5 h-5 rounded text-green-600">
                    <div>
                        <p class="font-medium text-gray-800">Üretim devam etti ✅</p>
                        <p class="text-sm text-gray-500">Arıza oluşmasına rağmen ocak/üretim durmadı</p>
                    </div>
                </label>
            </div>

            {{-- Açıklama --}}
            <div>
                <label for="description" class="form-label">Arıza Açıklaması</label>
                <textarea id="description" name="description" rows="4" class="form-input"
                    placeholder="Arızanın nasıl oluştuğunu, belirtilerini açıklayın...">{{ old('description') }}</textarea>
            </div>

            {{-- Tarih (opsiyonel) --}}
            <div>
                <label for="reported_at" class="form-label">Arıza Zamanı <span class="text-gray-400 text-xs">(boş bırakırsanız şu an kaydedilir)</span></label>
                <input type="datetime-local" id="reported_at" name="reported_at"
                    value="{{ old('reported_at') }}"
                    max="{{ now()->format('Y-m-d\TH:i') }}"
                    class="form-input">
                @error('reported_at') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

        </div>

        <div class="flex gap-3">
            <button type="submit" class="btn btn-accent flex-1">
                ⚠️ Arızayı Kaydet
            </button>
            <a href="{{ route('fault-reports.index') }}" class="btn btn-secondary">
                İptal
            </a>
        </div>

    </form>
</div>

<script>
// Öncelik radio butonları için görsel geri bildirim
document.querySelectorAll('input[name="priority"]').forEach(radio => {
    radio.addEventListener('change', function() {
        document.querySelectorAll('input[name="priority"]').forEach(r => {
            r.closest('label').style.outline = 'none';
        });
        this.closest('label').style.outline = '2px solid #3b82f6';
        this.closest('label').style.outlineOffset = '2px';
    });
});
</script>
@endsection
