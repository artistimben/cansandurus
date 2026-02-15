<?php

namespace App\Http\Controllers;

use App\Models\DowntimeRecord;
use App\Models\Machine;
use App\Models\ErrorCode;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

/**
 * Downtime Controller - Duruş İşlemleri
 * 
 * Duruş başlatma, bitirme ve görüntüleme işlemleri
 */
class DowntimeController extends Controller
{
    /**
     * Aktif duruşları listele
     */
    public function index()
    {
        $activeDowntimes = DowntimeRecord::with(['machine', 'errorCode', 'startedBy'])
            ->active()
            ->latest()
            ->paginate(20);

        return view('downtime.index', compact('activeDowntimes'));
    }

    /**
     * Duruş başlatma formu
     */
    public function create()
    {
        $machines = Machine::active()->orderBy('code')->get();
        $errorCodes = ErrorCode::active()->orderBy('category')->orderBy('code')->get();

        // Hata kodlarını kategorilere göre grupla
        $errorCodesByCategory = $errorCodes->groupBy('category');

        return view('downtime.create', compact('machines', 'errorCodesByCategory'));
    }

    /**
     * Duruş başlat
     */
    public function store(Request $request)
    {
        // Geçmiş kayıt mı yoksa şimdiki zaman kaydı mı?
        $isHistorical = $request->boolean('is_historical');

        // Validation kuralları
        $rules = [
            'machine_id' => ['required', 'exists:machines,id'],
            'error_code_id' => ['required', 'exists:error_codes,id'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];

        $messages = [
            'machine_id.required' => 'Lütfen bir makine seçin.',
            'machine_id.exists' => 'Geçersiz makine.',
            'error_code_id.required' => 'Lütfen bir hata kodu seçin.',
            'error_code_id.exists' => 'Geçersiz hata kodu.',
            'started_at.required' => 'Başlangıç tarihi zorunludur.',
            'started_at.date' => 'Geçerli bir tarih giriniz.',
            'started_at.before_or_equal' => 'Başlangıç tarihi gelecekte olamaz.',
            'ended_at.required' => 'Bitiş tarihi zorunludur.',
            'ended_at.date' => 'Geçerli bir tarih giriniz.',
            'ended_at.after' => 'Bitiş tarihi başlangıç tarihinden sonra olmalıdır.',
            'ended_at.before_or_equal' => 'Bitiş tarihi gelecekte olamaz.',
        ];

        // Geçmiş kayıt ise ek validation
        if ($isHistorical) {
            $rules['started_at'] = ['required', 'date', 'before_or_equal:now'];
            $rules['ended_at'] = ['required', 'date', 'after:started_at', 'before_or_equal:now'];
        }

        $validated = $request->validate($rules, $messages);

        // Şimdiki zaman kaydı için aktif duruş kontrolü
        if (!$isHistorical) {
            $existingDowntime = DowntimeRecord::where('machine_id', $validated['machine_id'])
                ->active()
                ->first();

            if ($existingDowntime) {
                return back()
                    ->withInput()
                    ->withErrors(['machine_id' => 'Bu makinede zaten aktif bir duruş var. Lütfen önce onu kapatın.']);
            }
        }

        // Duruş kaydı oluştur
        $downtimeData = [
            'machine_id' => $validated['machine_id'],
            'error_code_id' => $validated['error_code_id'],
            'started_by' => auth()->id(),
            'notes' => $validated['notes'],
        ];

        if ($isHistorical) {
            // Geçmiş kayıt - direkt tamamlanmış olarak kaydet
            $startedAt = \Carbon\Carbon::parse($validated['started_at']);
            $endedAt = \Carbon\Carbon::parse($validated['ended_at']);
            $durationMinutes = $startedAt->diffInMinutes($endedAt);

            $downtimeData['started_at'] = $startedAt;
            $downtimeData['ended_at'] = $endedAt;
            $downtimeData['ended_by'] = auth()->id();
            $downtimeData['duration_minutes'] = $durationMinutes;
            $downtimeData['status'] = 'completed';
        } else {
            // Normal kayıt - şimdi başlat
            $downtimeData['started_at'] = now();
            $downtimeData['status'] = 'active';
        }

        $downtime = DowntimeRecord::create($downtimeData);

        // Activity log
        $logDescription = $isHistorical
            ? "Geçmiş duruş kaydedildi - Süre: {$downtime->duration_minutes} dakika"
            : 'Duruş başlatıldı';

        ActivityLog::createLog(
            userId: auth()->id(),
            action: 'create',
            description: $logDescription,
            modelType: 'DowntimeRecord',
            modelId: $downtime->id,
            newValues: $downtime->toArray()
        );

        $successMessage = $isHistorical
            ? "Geçmiş duruş başarıyla kaydedildi. Süre: {$downtime->duration_minutes} dakika"
            : 'Duruş başarıyla başlatıldı.';

        return redirect()
            ->route('downtime.index')
            ->with('success', $successMessage);
    }

    /**
     * Duruş detayını göster
     */
    public function show(DowntimeRecord $downtime)
    {
        $downtime->load(['machine', 'errorCode', 'startedBy', 'endedBy']);

        return view('downtime.show', compact('downtime'));
    }

    /**
     * Duruş düzenleme formu (notlar için)
     */
    public function edit(DowntimeRecord $downtime)
    {
        // Sadece aktif duruşlar düzenlenebilir
        if (!$downtime->isActive()) {
            return redirect()
                ->route('downtime.index')
                ->withErrors(['error' => 'Sadece aktif duruşlar düzenlenebilir.']);
        }

        return view('downtime.edit', compact('downtime'));
    }

    /**
     * Duruş notlarını güncelle
     */
    public function update(Request $request, DowntimeRecord $downtime)
    {
        $validated = $request->validate([
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $oldValues = $downtime->toArray();

        $downtime->update($validated);

        // Activity log
        ActivityLog::createLog(
            userId: auth()->id(),
            action: 'update',
            description: 'Duruş notları güncellendi',
            modelType: 'DowntimeRecord',
            modelId: $downtime->id,
            oldValues: ['notes' => $oldValues['notes']],
            newValues: ['notes' => $validated['notes']]
        );

        return redirect()
            ->route('downtime.show', $downtime)
            ->with('success', 'Duruş notları güncellendi.');
    }

    /**
     * Duruşu bitir
     */
    public function complete(DowntimeRecord $downtime)
    {
        // Duruş zaten tamamlanmış mı?
        if (!$downtime->isActive()) {
            return redirect()
                ->route('downtime.index')
                ->withErrors(['error' => 'Bu duruş zaten tamamlanmış.']);
        }

        $oldValues = $downtime->toArray();

        // Duruşu tamamla
        $downtime->complete(auth()->id());

        // Activity log
        ActivityLog::createLog(
            userId: auth()->id(),
            action: 'complete',
            description: 'Duruş tamamlandı - Süre: ' . $downtime->duration_minutes . ' dakika',
            modelType: 'DowntimeRecord',
            modelId: $downtime->id,
            oldValues: ['status' => $oldValues['status']],
            newValues: [
                'status' => 'completed',
                'ended_at' => $downtime->ended_at->toDateTimeString(),
                'duration_minutes' => $downtime->duration_minutes
            ]
        );

        return redirect()
            ->route('downtime.index')
            ->with('success', "Duruş tamamlandı. Toplam süre: {$downtime->duration_minutes} dakika");
    }

    /**
     * Duruşu iptal et (soft delete)
     */
    public function destroy(DowntimeRecord $downtime)
    {
        // Sadece aktif duruşlar iptal edilebilir
        if (!$downtime->isActive()) {
            return redirect()
                ->route('downtime.index')
                ->withErrors(['error' => 'Sadece aktif duruşlar iptal edilebilir.']);
        }

        $downtime->update(['status' => 'cancelled']);
        $downtime->delete();

        // Activity log
        ActivityLog::createLog(
            userId: auth()->id(),
            action: 'delete',
            description: 'Duruş iptal edildi',
            modelType: 'DowntimeRecord',
            modelId: $downtime->id
        );

        return redirect()
            ->route('downtime.index')
            ->with('success', 'Duruş iptal edildi.');
    }
}
