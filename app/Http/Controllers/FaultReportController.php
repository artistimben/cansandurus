<?php

namespace App\Http\Controllers;

use App\Models\FaultReport;
use App\Models\Machine;
use App\Models\ErrorCode;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

/**
 * FaultReportController - Duruşsuz Arıza Bildirimleri
 *
 * Ocakların durmasına yol açmayan ancak
 * fabrikada meydana gelen arızaların kaydedilmesi.
 */
class FaultReportController extends Controller
{
    /**
     * Arıza listesi
     */
    public function index(Request $request)
    {
        $query = FaultReport::with(['machine', 'errorCode', 'reportedBy'])
            ->latest('reported_at');

        // Filtreler
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }
        if ($request->filled('machine_id')) {
            $query->where('machine_id', $request->machine_id);
        }

        $faultReports = $query->paginate(20)->withQueryString();

        // Özet istatistikler
        $stats = [
            'open' => FaultReport::where('status', 'open')->count(),
            'in_progress' => FaultReport::where('status', 'in_progress')->count(),
            'resolved' => FaultReport::where('status', 'resolved')->count(),
            'critical' => FaultReport::where('priority', 'critical')->whereIn('status', ['open', 'in_progress'])->count(),
        ];

        $machines = Machine::active()->orderBy('code')->get();

        return view('fault-reports.index', compact('faultReports', 'stats', 'machines'));
    }

    /**
     * Arıza bildirim formu
     */
    public function create()
    {
        $machines = Machine::active()->orderBy('code')->get();
        $errorCodes = ErrorCode::active()->orderBy('category')->orderBy('code')->get();
        $errorsByCategory = $errorCodes->groupBy('category');

        return view('fault-reports.create', compact('machines', 'errorsByCategory'));
    }

    /**
     * Arıza bildir
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'machine_id' => ['required', 'exists:machines,id'],
            'error_code_id' => ['nullable', 'exists:error_codes,id'],
            'title' => ['required', 'string', 'max:200'],
            'description' => ['nullable', 'string', 'max:2000'],
            'priority' => ['required', 'in:low,medium,high,critical'],
            'production_continued' => ['boolean'],
            'reported_at' => ['nullable', 'date', 'before_or_equal:now'],
        ], [
            'machine_id.required' => 'Lütfen bir makine seçin.',
            'machine_id.exists' => 'Geçersiz makine.',
            'title.required' => 'Arıza başlığı zorunludur.',
            'title.max' => 'Başlık en fazla 200 karakter olabilir.',
            'priority.required' => 'Öncelik seviyesi seçiniz.',
            'priority.in' => 'Geçersiz öncelik.',
            'reported_at.before_or_equal' => 'Tarih gelecekte olamaz.',
        ]);

        $faultReport = FaultReport::create([
            'machine_id' => $validated['machine_id'],
            'error_code_id' => $validated['error_code_id'] ?? null,
            'reported_by' => auth()->id(),
            'reported_at' => $validated['reported_at'] ?? now(),
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'priority' => $validated['priority'],
            'production_continued' => $request->boolean('production_continued', true),
            'status' => 'open',
        ]);

        ActivityLog::createLog(
            userId: auth()->id(),
            action: 'create',
            description: "Arıza bildirimi oluşturuldu: {$faultReport->title}",
            modelType: 'FaultReport',
            modelId: $faultReport->id,
            newValues: $faultReport->toArray()
        );

        return redirect()
            ->route('fault-reports.show', $faultReport)
            ->with('success', 'Arıza bildirimi başarıyla kaydedildi.');
    }

    /**
     * Arıza detayı
     */
    public function show(FaultReport $faultReport)
    {
        $faultReport->load(['machine', 'errorCode', 'reportedBy', 'resolvedBy']);
        return view('fault-reports.show', compact('faultReport'));
    }

    /**
     * Arıza düzenleme formu
     */
    public function edit(FaultReport $faultReport)
    {
        if ($faultReport->isResolved() && !in_array(auth()->user()->role, ['admin', 'manager'])) {
            return redirect()->route('fault-reports.show', $faultReport)
                ->withErrors(['error' => 'Çözülmüş arızalar yalnızca yönetici tarafından düzenlenebilir.']);
        }

        $machines = Machine::active()->orderBy('code')->get();
        $errorCodes = ErrorCode::active()->orderBy('category')->orderBy('code')->get();
        $errorsByCategory = $errorCodes->groupBy('category');

        return view('fault-reports.edit', compact('faultReport', 'machines', 'errorsByCategory'));
    }

    /**
     * Arıza güncelle
     */
    public function update(Request $request, FaultReport $faultReport)
    {
        $validated = $request->validate([
            'machine_id' => ['required', 'exists:machines,id'],
            'error_code_id' => ['nullable', 'exists:error_codes,id'],
            'title' => ['required', 'string', 'max:200'],
            'description' => ['nullable', 'string', 'max:2000'],
            'action_taken' => ['nullable', 'string', 'max:2000'],
            'priority' => ['required', 'in:low,medium,high,critical'],
            'status' => ['required', 'in:open,in_progress,resolved'],
            'production_continued' => ['boolean'],
        ]);

        $oldValues = $faultReport->toArray();

        // Durum "resolved" oluyorsa çözüm bilgilerini kaydet
        if ($validated['status'] === 'resolved' && !$faultReport->isResolved()) {
            $validated['resolved_by'] = auth()->id();
            $validated['resolved_at'] = now();
        }

        $faultReport->update($validated);

        ActivityLog::createLog(
            userId: auth()->id(),
            action: 'update',
            description: "Arıza bildirimi güncellendi: {$faultReport->title}",
            modelType: 'FaultReport',
            modelId: $faultReport->id,
            oldValues: $oldValues,
            newValues: $faultReport->fresh()->toArray()
        );

        return redirect()
            ->route('fault-reports.show', $faultReport)
            ->with('success', 'Arıza bildirimi güncellendi.');
    }

    /**
     * Hızlı çözüm (AJAX friendly)
     */
    public function resolve(Request $request, FaultReport $faultReport)
    {
        $validated = $request->validate([
            'action_taken' => ['nullable', 'string', 'max:2000'],
        ]);

        $faultReport->update([
            'status' => 'resolved',
            'resolved_by' => auth()->id(),
            'resolved_at' => now(),
            'action_taken' => $validated['action_taken'] ?? null,
        ]);

        ActivityLog::createLog(
            userId: auth()->id(),
            action: 'resolve',
            description: "Arıza çözüldü: {$faultReport->title}",
            modelType: 'FaultReport',
            modelId: $faultReport->id,
            newValues: ['status' => 'resolved', 'resolved_at' => now()]
        );

        return redirect()
            ->route('fault-reports.index')
            ->with('success', "Arıza bildirimi çözüldü olarak işaretlendi.");
    }

    /**
     * Arıza sil (admin/manager)
     */
    public function destroy(FaultReport $faultReport)
    {
        if (!in_array(auth()->user()->role, ['admin', 'manager'])) {
            return redirect()->back()->withErrors(['error' => 'Bu işlem için yetkiniz yok.']);
        }

        ActivityLog::createLog(
            userId: auth()->id(),
            action: 'delete',
            description: "Arıza bildirimi silindi: {$faultReport->title}",
            modelType: 'FaultReport',
            modelId: $faultReport->id,
            oldValues: $faultReport->toArray()
        );

        $faultReport->forceDelete();

        return redirect()
            ->route('fault-reports.index')
            ->with('success', 'Arıza bildirimi kalıcı olarak silindi.');
    }
}
