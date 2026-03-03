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

        <div class="card space-y-5">

            {{-- Makine --}}
            <div>
                <label for="machine_id" class="form-label">Makine / Ocak <span class="text-red-500">*</span></label>
                <select id="machine_id" name="machine_id" class="form-select" required>
                    @foreach($machines as $machine)
                        <option value="{{ $machine->id }}" {{ old('machine_id', $faultReport->machine_id) == $machine->id ? 'selected' : '' }}>
                            {{ $machine->code }} – {{ $machine->name }}
                        </option>
                    @endforeach
                </select>
                @error('machine_id') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Başlık --}}
            <div>
                <label for="title" class="form-label">Arıza Başlığı <span class="text-red-500">*</span></label>
                <input type="text" id="title" name="title"
                    value="{{ old('title', $faultReport->title) }}"
                    class="form-input" required maxlength="200">
                @error('title') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Hata Kodu --}}
            <div>
                <label for="error_code_id" class="form-label">Hata Kodu <span class="text-gray-400 text-xs">(opsiyonel)</span></label>
                <select id="error_code_id" name="error_code_id" class="form-select">
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

            {{-- Öncelik --}}
            <div>
                <label class="form-label">Öncelik Seviyesi</label>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mt-2">
                    @foreach(['low' => ['label'=>'Düşük','color'=>'border-gray-300 bg-gray-50 text-gray-700','icon'=>'🟢'],
                               'medium' => ['label'=>'Orta','color'=>'border-blue-300 bg-blue-50 text-blue-700','icon'=>'🔵'],
                               'high' => ['label'=>'Yüksek','color'=>'border-orange-300 bg-orange-50 text-orange-700','icon'=>'🟠'],
                               'critical' => ['label'=>'Kritik','color'=>'border-red-300 bg-red-50 text-red-700','icon'=>'🔴']] as $val => $info)
                        <label class="flex flex-col items-center p-3 border-2 rounded-lg cursor-pointer hover:opacity-80 transition {{ $info['color'] }}">
                            <input type="radio" name="priority" value="{{ $val }}"
                                {{ old('priority', $faultReport->priority) === $val ? 'checked' : '' }}
                                class="sr-only">
                            <span class="text-2xl">{{ $info['icon'] }}</span>
                            <span class="font-medium text-sm mt-1">{{ $info['label'] }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            {{-- Durum --}}
            <div>
                <label for="status" class="form-label">Durum</label>
                <select id="status" name="status" class="form-select">
                    <option value="open"        {{ old('status', $faultReport->status) === 'open'        ? 'selected' : '' }}>Açık</option>
                    <option value="in_progress" {{ old('status', $faultReport->status) === 'in_progress' ? 'selected' : '' }}>İşlemde</option>
                    <option value="resolved"    {{ old('status', $faultReport->status) === 'resolved'    ? 'selected' : '' }}>Çözüldü</option>
                </select>
            </div>

            {{-- Üretim --}}
            <div class="p-4 bg-gray-50 rounded-lg">
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" name="production_continued" value="1"
                        {{ old('production_continued', $faultReport->production_continued) ? 'checked' : '' }}
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
                <textarea id="description" name="description" rows="4" class="form-input">{{ old('description', $faultReport->description) }}</textarea>
            </div>

            {{-- Alınan Önlem --}}
            <div>
                <label for="action_taken" class="form-label">Alınan Önlem / Yapılan İşlem</label>
                <textarea id="action_taken" name="action_taken" rows="4" class="form-input"
                    placeholder="Yapılan müdahale, değiştirilen parçalar, alınan tedbirler...">{{ old('action_taken', $faultReport->action_taken) }}</textarea>
            </div>

        </div>

        <div class="flex gap-3">
            <button type="submit" class="btn btn-primary flex-1">💾 Kaydet</button>
            <a href="{{ route('fault-reports.show', $faultReport) }}" class="btn btn-secondary">İptal</a>
        </div>
    </form>

</div>
@endsection
