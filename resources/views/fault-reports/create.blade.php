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

        <div class="card space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Makine --}}
                <div class="space-y-1">
                    <label for="machine_id" class="form-label flex items-center gap-2">
                        <span>🏭 Makine / Ocak</span>
                        <span class="text-red-500">*</span>
                    </label>
                    <select id="machine_id" name="machine_id" class="form-select w-full" required>
                        <option value="">— Makine Seçin —</option>
                        @foreach($machines as $machine)
                            <option value="{{ $machine->id }}" {{ old('machine_id') == $machine->id ? 'selected' : '' }}>
                                {{ $machine->code }} – {{ $machine->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('machine_id') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Arıza Başlığı --}}
                <div class="space-y-1">
                    <label for="title" class="form-label flex items-center gap-2">
                        <span>📝 Arıza Başlığı</span>
                        <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="title" name="title"
                        value="{{ old('title') }}"
                        placeholder="Kısa ve açıklayıcı bir başlık girin"
                        class="form-input w-full" required maxlength="200">
                    @error('title') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Hata Kodu (opsiyonel) --}}
                <div class="space-y-1">
                    <label for="error_code_id" class="form-label flex items-center gap-2">
                        <span>🔍 Hata Kodu</span>
                        <span class="text-gray-400 text-xs">(opsiyonel)</span>
                    </label>
                    <select id="error_code_id" name="error_code_id" class="form-select w-full">
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

                {{-- Üretim devam etti mi? --}}
                <div class="bg-gray-50 p-4 rounded-xl border border-gray-100 flex items-center">
                    <label class="flex items-center gap-3 cursor-pointer w-full">
                        <input type="checkbox" name="production_continued" value="1"
                            {{ old('production_continued', '1') ? 'checked' : '' }}
                            class="w-6 h-6 rounded text-green-600 focus:ring-green-500">
                        <div>
                            <p class="font-bold text-gray-800">Üretim devam etti ✅</p>
                            <p class="text-xs text-gray-500">Arıza oluşmasına rağmen ocak durmadı</p>
                        </div>
                    </label>
                </div>
            </div>

            <hr class="border-gray-100">

            {{-- Zaman Bilgileri --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="reported_at" class="form-label flex items-center gap-2">
                        <span>🕒 Başlangıç Zamanı</span>
                    </label>
                    <input type="datetime-local" id="reported_at" name="reported_at"
                        value="{{ old('reported_at', now()->format('Y-m-d\TH:i')) }}"
                        class="form-input w-full">
                    <p class="text-xs text-gray-400 mt-1">Arızanın ilk fark edildiği an</p>
                    @error('reported_at') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="resolved_at" class="form-label flex items-center gap-2">
                        <span>🏁 Bitiş Zamanı</span>
                        <span class="text-gray-400 text-xs">(opsiyonel)</span>
                    </label>
                    <input type="datetime-local" id="resolved_at" name="resolved_at"
                        value="{{ old('resolved_at') }}"
                        class="form-input w-full">
                    <p class="text-xs text-gray-400 mt-1">Arıza giderildiyse doldurun, boş bırakırsanız "Açık" kalır</p>
                    @error('resolved_at') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            {{-- Öncelik --}}
            <div>
                <label class="form-label flex items-center gap-2 mb-3">
                    <span>⚡ Öncelik Seviyesi</span>
                    <span class="text-red-500">*</span>
                </label>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                    @foreach(['low' => ['label'=>'Düşük','color'=>'border-gray-200 bg-gray-50 text-gray-600','active'=>'ring-gray-400 bg-gray-100 border-gray-400 text-gray-900','icon'=>'🟢'],
                               'medium' => ['label'=>'Orta','color'=>'border-blue-100 bg-blue-50 text-blue-600','active'=>'ring-blue-400 bg-blue-100 border-blue-400 text-blue-900','icon'=>'🔵'],
                               'high' => ['label'=>'Yüksek','color'=>'border-orange-100 bg-orange-50 text-orange-600','active'=>'ring-orange-400 bg-orange-100 border-orange-400 text-orange-900','icon'=>'🟠'],
                               'critical' => ['label'=>'Kritik','color'=>'border-red-100 bg-red-50 text-red-600','active'=>'ring-red-400 bg-red-100 border-red-400 text-red-900','icon'=>'🔴']] as $val => $info)
                        <label class="priority-label flex flex-col items-center p-4 border-2 rounded-2xl cursor-pointer hover:shadow-md transition-all
                            {{ old('priority', 'medium') === $val ? $info['active'] : $info['color'] }}">
                            <input type="radio" name="priority" value="{{ $val }}"
                                {{ old('priority', 'medium') === $val ? 'checked' : '' }}
                                class="sr-only">
                            <span class="text-3xl mb-1">{{ $info['icon'] }}</span>
                            <span class="font-bold text-sm">{{ $info['label'] }}</span>
                        </label>
                    @endforeach
                </div>
                @error('priority') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Açıklama --}}
            <div>
                <label for="description" class="form-label flex items-center gap-2">
                    <span>💬 Arıza Açıklaması</span>
                </label>
                <textarea id="description" name="description" rows="4" class="form-input w-full"
                    placeholder="Arızanın nasıl oluştuğunu, belirtilerini açıklayın...">{{ old('description') }}</textarea>
            </div>

        </div>

        <div class="flex flex-col sm:flex-row gap-4 pt-2">
            <button type="submit" class="btn btn-accent flex-1 text-lg py-4 shadow-lg hover:shadow-xl transform hover:-translate-y-1 transition-all">
                ⚠️ Arızayı Kaydet
            </button>
            <a href="{{ route('fault-reports.index') }}" class="btn btn-secondary py-4 px-8 flex items-center justify-center">
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
