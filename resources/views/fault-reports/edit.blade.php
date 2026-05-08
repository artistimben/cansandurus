@extends('layouts.app')

@section('title', 'Arıza Düzenle')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    <div class="flex items-center gap-4">
        <a href="{{ route('fault-reports.show', $faultReport) }}" class="text-gray-500 hover:text-gray-700">← Geri</a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900">✏️ Arıza Düzenle</h1>
            <p class="text-gray-500 text-sm">#{{ $faultReport->id }} – {{ $faultReport->title }}</p>
        </div>
    </div>

    <form method="POST" action="{{ route('fault-reports.update', $faultReport) }}" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="card space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Makine --}}
                <div class="space-y-1">
                    <label for="machine_id" class="form-label flex items-center gap-2">
                        <span>🏭 Makine / Ocak</span>
                        <span class="text-red-500">*</span>
                    </label>
                    <select id="machine_id" name="machine_id" class="form-select w-full" required>
                        @foreach($machines as $machine)
                            <option value="{{ $machine->id }}" {{ old('machine_id', $faultReport->machine_id) == $machine->id ? 'selected' : '' }}>
                                {{ $machine->code }} – {{ $machine->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('machine_id') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Başlık --}}
                <div class="space-y-1">
                    <label for="title" class="form-label flex items-center gap-2">
                        <span>📝 Arıza Başlığı</span>
                        <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="title" name="title"
                        value="{{ old('title', $faultReport->title) }}"
                        class="form-input w-full" required maxlength="200">
                    @error('title') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Hata Kodu --}}
                <div class="space-y-1">
                    <label for="error_code_id" class="form-label flex items-center gap-2">
                        <span>🔍 Hata Kodu</span>
                        <span class="text-gray-400 text-xs">(opsiyonel)</span>
                    </label>
                    <select id="error_code_id" name="error_code_id" class="form-select w-full">
                        <option value="">— Seçiniz —</option>
                        @foreach($errorsByCategory as $category => $codes)
                            <optgroup label="{{ $category }}">
                                @foreach($codes as $code)
                                    <option value="{{ $code->id }}" {{ old('error_code_id', $faultReport->error_code_id) == $code->id ? 'selected' : '' }}>
                                        {{ $code->code }} – {{ $code->name }}
                                    </option>
                                @endforeach
                            </optgroup>
                        @endforeach
                    </select>
                </div>

                {{-- Durum --}}
                <div class="space-y-1">
                    <label for="status" class="form-label flex items-center gap-2">
                        <span>📊 Durum</span>
                    </label>
                    <select id="status" name="status" class="form-select w-full">
                        <option value="open"        {{ old('status', $faultReport->status) === 'open'        ? 'selected' : '' }}>🔴 Açık</option>
                        <option value="in_progress" {{ old('status', $faultReport->status) === 'in_progress' ? 'selected' : '' }}>🟠 İşlemde</option>
                        <option value="resolved"    {{ old('status', $faultReport->status) === 'resolved'    ? 'selected' : '' }}>🟢 Çözüldü</option>
                    </select>
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
                        value="{{ old('reported_at', $faultReport->reported_at?->format('Y-m-d\TH:i')) }}"
                        class="form-input w-full">
                    @error('reported_at') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="resolved_at" class="form-label flex items-center gap-2">
                        <span>🏁 Bitiş Zamanı</span>
                        <span class="text-gray-400 text-xs">(opsiyonel)</span>
                    </label>
                    <input type="datetime-local" id="resolved_at" name="resolved_at"
                        value="{{ old('resolved_at', $faultReport->resolved_at?->format('Y-m-d\TH:i')) }}"
                        class="form-input w-full">
                    @error('resolved_at') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            {{-- Üretim --}}
            <div class="bg-gray-50 p-4 rounded-xl border border-gray-100">
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" name="production_continued" value="1"
                        {{ old('production_continued', $faultReport->production_continued) ? 'checked' : '' }}
                        class="w-6 h-6 rounded text-green-600 focus:ring-green-500">
                    <div>
                        <p class="font-bold text-gray-800">Üretim devam etti ✅</p>
                        <p class="text-xs text-gray-500">Arıza oluşmasına rağmen ocak durmadı</p>
                    </div>
                </label>
            </div>

            {{-- Öncelik --}}
            <div>
                <label class="form-label flex items-center gap-2 mb-3">
                    <span>⚡ Öncelik Seviyesi</span>
                </label>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                    @foreach(['low' => ['label'=>'Düşük','icon'=>'🟢'],
                               'medium' => ['label'=>'Orta','icon'=>'🔵'],
                               'high' => ['label'=>'Yüksek','icon'=>'🟠'],
                               'critical' => ['label'=>'Kritik','icon'=>'🔴']] as $val => $info)
                        <label class="flex flex-col items-center p-4 border-2 rounded-2xl cursor-pointer hover:shadow-md transition-all
                            {{ old('priority', $faultReport->priority) === $val ? 'ring-2 ring-primary-500 border-primary-500 bg-primary-50' : 'border-gray-200 bg-gray-50' }}">
                            <input type="radio" name="priority" value="{{ $val }}"
                                {{ old('priority', $faultReport->priority) === $val ? 'checked' : '' }}
                                class="sr-only">
                            <span class="text-3xl mb-1">{{ $info['icon'] }}</span>
                            <span class="font-bold text-sm">{{ $info['label'] }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            {{-- Açıklama --}}
            <div>
                <label for="description" class="form-label flex items-center gap-2">
                    <span>💬 Arıza Açıklaması</span>
                </label>
                <textarea id="description" name="description" rows="3" class="form-input w-full">{{ old('description', $faultReport->description) }}</textarea>
            </div>

            {{-- Alınan Önlem --}}
            <div>
                <label for="action_taken" class="form-label flex items-center gap-2">
                    <span>🛠️ Alınan Önlem / Yapılan İşlem</span>
                </label>
                <textarea id="action_taken" name="action_taken" rows="3" class="form-input w-full"
                    placeholder="Yapılan müdahale, değiştirilen parçalar...">{{ old('action_taken', $faultReport->action_taken) }}</textarea>
            </div>

        </div>

        <div class="flex flex-col sm:flex-row gap-4 pt-2">
            <button type="submit" class="btn btn-primary flex-1 text-lg py-4 shadow-lg hover:shadow-xl transform hover:-translate-y-1 transition-all">
                💾 Değişiklikleri Kaydet
            </button>
            <a href="{{ route('fault-reports.show', $faultReport) }}" class="btn btn-secondary py-4 px-8 flex items-center justify-center">
                İptal
            </a>
        </div>
    </form>

</div>
@endsection
