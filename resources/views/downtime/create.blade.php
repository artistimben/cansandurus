@extends('layouts.app')

@section('title', 'Yeni Duruş Başlat')

@section('content')
<div class="max-w-2xl mx-auto px-4">
    <div class="mb-6">
        <a href="{{ route('downtime.index') }}" class="text-primary-600 hover:text-primary-800">
            ← Geri Dön
        </a>
    </div>

    <div class="card">
        <h1 class="text-2xl font-bold text-gray-900 mb-6">Yeni Duruş Başlat</h1>

            <form method="POST" action="{{ route('downtime.store') }}" class="space-y-6 w-full overflow-x-hidden">
                @csrf

            <!-- Geçmiş Duruş Kaydı Toggle -->
            <div class="bg-accent-50 border border-accent-200 rounded-lg p-4 w-full">
                <label class="flex items-center cursor-pointer">
                    <input 
                        type="checkbox" 
                        id="historical_toggle" 
                        name="is_historical" 
                        value="1"
                        class="w-5 h-5 text-accent-600 rounded focus:ring-accent-500"
                        {{ old('is_historical') ? 'checked' : '' }}
                    >
                    <span class="ml-3 font-semibold text-accent-900">
                        📅 Geçmişe Dönük Duruş Kaydı Ekle
                    </span>
                </label>
                <p class="text-sm text-accent-700 mt-2 ml-8">
                    Bu seçeneği işaretlerseniz, geçmiş bir tarih/saatte gerçekleşen duruşu sisteme girebilirsiniz.
                </p>
            </div>

            <!-- Geçmiş Tarih Alanları (Başlangıçta gizli) -->
            <div id="historical_fields" class="space-y-4 mb-6" style="display: none;">
                <!-- Başlangıç Tarihi/Saati -->
                <div>
                    <label for="started_at" class="label">Başlangıç Tarihi ve Saati *</label>
                    <input 
                        type="datetime-local" 
                        id="started_at" 
                        name="started_at" 
                        class="input @error('started_at') border-red-500 @enderror"
                        value="{{ old('started_at') }}"
                        max="{{ date('Y-m-d\TH:i') }}"
                    >
                    @error('started_at')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Bitiş Tarihi/Saati -->
                <div>
                    <label for="ended_at" class="label">Bitiş Tarihi ve Saati *</label>
                    <input 
                        type="datetime-local" 
                        id="ended_at" 
                        name="ended_at" 
                        class="input @error('ended_at') border-red-500 @enderror"
                        value="{{ old('ended_at') }}"
                        max="{{ date('Y-m-d\TH:i') }}"
                    >
                    @error('ended_at')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                    <p class="text-gray-500 text-sm mt-1">Bitiş zamanı başlangıç zamanından sonra olmalıdır</p>
                </div>
            </div>

                <!-- Makine Seçimi -->
                <div>
                    <label for="machine_id" class="label">Makine *</label>
                    <select id="machine_id" name="machine_id" class="input @error('machine_id') border-red-500 @enderror"
                        required>
                        <option value="">Makine seçin...</option>
                        @foreach($machines as $machine)
                            <option value="{{ $machine->id }}" {{ old('machine_id') == $machine->id ? 'selected' : '' }}>
                                {{ $machine->code }} - {{ $machine->name }} ({{ $machine->location }})
                            </option>
                        @endforeach
                    </select>
                    @error('machine_id')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Hata Kodu Seçimi -->
                <div>
                    <label for="error_code_id" class="label">Hata Kodu *</label>
                    <select id="error_code_id" name="error_code_id"
                        class="input @error('error_code_id') border-red-500 @enderror" required>
                        <option value="">Hata kodu seçin...</option>
                        @foreach($errorCodesByCategory as $category => $codes)
                            <optgroup label="{{ $category }}">
                                @foreach($codes as $code)
                                    <option value="{{ $code->id }}" {{ old('error_code_id') == $code->id ? 'selected' : '' }}>
                                        {{ $code->code }} - {{ $code->name }}
                                    </option>
                                @endforeach
                            </optgroup>
                        @endforeach
                    </select>
                    @error('error_code_id')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Notlar -->
                <div>
                    <label for="notes" class="label">Notlar (Opsiyonel)</label>
                    <textarea id="notes" name="notes" rows="4" class="input @error('notes') border-red-500 @enderror"
                        placeholder="Duruş hakkında detaylı bilgi girebilirsiniz...">{{ old('notes') }}</textarea>
                    @error('notes')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                    <p class="text-gray-500 text-sm mt-1">Maksimum 1000 karakter</p>
                </div>

                <!-- Butonlar -->
                <div class="flex justify-end space-x-3">
                    <a href="{{ route('downtime.index') }}" class="btn btn-secondary">
                        İptal
                    </a>
                    <button type="submit" class="btn btn-accent">
                        🚨 Duruşu Başlat
                    </button>
                </div>
            </form>
        </div>

        <!-- Bilgilendirme -->
    <div class="mt-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
        <h3 class="font-semibold text-blue-900 mb-2">ℹ️ Bilgilendirme</h3>
        <ul class="text-sm text-blue-800 space-y-1 list-disc list-inside">
            <li>Normal duruş: Şu anki zaman otomatik kaydedilir</li>
            <li>Geçmiş duruş: Manuel tarih/saat girerek geçmişteki duruşları kaydedebilirsiniz</li>
            <li>Aynı makine için birden fazla aktif duruş olamaz</li>
            <li>Geçmiş duruşlar otomatik olarak tamamlanmış şekilde kaydedilir</li>
            <li>Tüm işlemler güvenlik loglarına kaydedilmektedir</li>
        </ul>
    </div>
</div>

<script>
    // Geçmiş duruş toggle işlevi
    const historicalToggle = document.getElementById('historical_toggle');
    const historicalFields = document.getElementById('historical_fields');
    const startedAtInput = document.getElementById('started_at');
    const endedAtInput = document.getElementById('ended_at');
    const form = document.querySelector('form');

    // Toggle durumunu yönet
    function handleToggle() {
        if (historicalToggle.checked) {
            historicalFields.style.display = 'block';
            startedAtInput.required = true;
            endedAtInput.required = true;
        } else {
            historicalFields.style.display = 'none';
            startedAtInput.required = false;
            endedAtInput.required = false;
            startedAtInput.value = '';
            endedAtInput.value = '';
        }
    }

    // Sayfa yüklendiğinde kontrol et (validation hatası varsa)
    if (historicalToggle.checked) {
        handleToggle();
    }

    historicalToggle.addEventListener('change', handleToggle);

    // Form validation - bitiş zamanı başlangıçtan sonra mı?
    form.addEventListener('submit', function(e) {
        if (historicalToggle.checked) {
            const startedAt = new Date(startedAtInput.value);
            const endedAt = new Date(endedAtInput.value);

            if (endedAt <= startedAt) {
                e.preventDefault();
                alert('Bitiş zamanı başlangıç zamanından sonra olmalıdır!');
                endedAtInput.focus();
                return false;
            }

            // Gelecek tarih kontrolü
            const now = new Date();
            if (startedAt > now || endedAt > now) {
                e.preventDefault();
                alert('Gelecek tarihli duruş kaydedilemez!');
                return false;
            }
        }
    });

    // Başlangıç zamanı değiştiğinde bitiş zamanının min değerini güncelle
    startedAtInput.addEventListener('change', function() {
        endedAtInput.min = this.value;
    });
</script>
@endsection