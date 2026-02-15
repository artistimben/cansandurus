<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Machine;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

/**
 * Machine Controller - Makine Yönetimi
 * 
 * Sadece admin erişebilir
 */
class MachineController extends Controller
{
    /**
     * Makineleri listele
     */
    public function index()
    {
        $machines = Machine::withCount('downtimeRecords')
            ->orderBy('code')
            ->paginate(20);

        return view('admin.machines.index', compact('machines'));
    }

    /**
     * Yeni makine oluşturma formu
     */
    public function create()
    {
        return view('admin.machines.create');
    }

    /**
     * Yeni makine kaydet
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:50', 'unique:machines,code'],
            'name' => ['required', 'string', 'max:100'],
            'location' => ['nullable', 'string', 'max:100'],
            'description' => ['nullable', 'string'],
            'is_active' => ['boolean'],
        ], [
            'code.required' => 'Makine kodu zorunludur.',
            'code.unique' => 'Bu makine kodu zaten kullanılıyor.',
            'name.required' => 'Makine adı zorunludur.',
        ]);

        $machine = Machine::create($validated);

        // Activity log
        ActivityLog::createLog(
            userId: auth()->id(),
            action: 'create',
            description: 'Yeni makine oluşturuldu: ' . $machine->name,
            modelType: 'Machine',
            modelId: $machine->id,
            newValues: $machine->toArray()
        );

        return redirect()
            ->route('admin.machines.index')
            ->with('success', 'Makine başarıyla oluşturuldu.');
    }

    /**
     * Makine detayını göster
     */
    public function show(Machine $machine)
    {
        $machine->load(['downtimeRecords' => function ($query) {
            $query->with(['errorCode', 'startedBy'])->latest()->take(20);
        }]);

        return view('admin.machines.show', compact('machine'));
    }

    /**
     * Makine düzenleme formu
     */
    public function edit(Machine $machine)
    {
        return view('admin.machines.edit', compact('machine'));
    }

    /**
     * Makine güncelle
     */
    public function update(Request $request, Machine $machine)
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:50', 'unique:machines,code,' . $machine->id],
            'name' => ['required', 'string', 'max:100'],
            'location' => ['nullable', 'string', 'max:100'],
            'description' => ['nullable', 'string'],
            'is_active' => ['boolean'],
        ]);

        $oldValues = $machine->toArray();
        $machine->update($validated);

        // Activity log
        ActivityLog::createLog(
            userId: auth()->id(),
            action: 'update',
            description: 'Makine güncellendi: ' . $machine->name,
            modelType: 'Machine',
            modelId: $machine->id,
            oldValues: $oldValues,
            newValues: $machine->fresh()->toArray()
        );

        return redirect()
            ->route('admin.machines.index')
            ->with('success', 'Makine başarıyla güncellendi.');
    }

    /**
     * Makineyi sil (soft delete)
     */
    public function destroy(Machine $machine)
    {
        // Aktif duruşu var mı kontrol et
        if ($machine->activeDowntimeRecords()->exists()) {
            return back()->withErrors([
                'error' => 'Bu makinede aktif duruşlar var. Önce duruşları kapatın.'
            ]);
        }

        $machineName = $machine->name;
        $machine->delete();

        // Activity log
        ActivityLog::createLog(
            userId: auth()->id(),
            action: 'delete',
            description: 'Makine silindi: ' . $machineName,
            modelType: 'Machine',
            modelId: $machine->id
        );

        return redirect()
            ->route('admin.machines.index')
            ->with('success', 'Makine başarıyla silindi.');
    }
}
