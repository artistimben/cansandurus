<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ErrorCode;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

/**
 * ErrorCode Controller - Hata Kodu Yönetimi
 * 
 * Sadece admin erişebilir
 */
class ErrorCodeController extends Controller
{
    /**
     * Hata kodlarını listele
     */
    public function index()
    {
        $errorCodes = ErrorCode::withCount('downtimeRecords')
            ->orderBy('category')
            ->orderBy('code')
            ->paginate(20);

        $categories = ErrorCode::getCategories();

        return view('admin.error-codes.index', compact('errorCodes', 'categories'));
    }

    /**
     * Yeni hata kodu oluşturma formu
     */
    public function create()
    {
        $categories = ErrorCode::getCategories();
        return view('admin.error-codes.create', compact('categories'));
    }

    /**
     * Yeni hata kodu kaydet
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:50', 'unique:error_codes,code'],
            'category' => ['required', 'string', 'max:100'],
            'name' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string'],
            'is_active' => ['boolean'],
        ], [
            'code.required' => 'Hata kodu zorunludur.',
            'code.unique' => 'Bu hata kodu zaten kullanılıyor.',
            'category.required' => 'Kategori zorunludur.',
            'name.required' => 'Hata adı zorunludur.',
        ]);

        $errorCode = ErrorCode::create($validated);

        // Activity log
        ActivityLog::createLog(
            userId: auth()->id(),
            action: 'create',
            description: 'Yeni hata kodu oluşturuldu: ' . $errorCode->code,
            modelType: 'ErrorCode',
            modelId: $errorCode->id,
            newValues: $errorCode->toArray()
        );

        return redirect()
            ->route('admin.error-codes.index')
            ->with('success', 'Hata kodu başarıyla oluşturuldu.');
    }

    /**
     * Hata kodu detayını göster
     */
    public function show(ErrorCode $errorCode)
    {
        $errorCode->load(['downtimeRecords' => function ($query) {
            $query->with(['machine', 'startedBy'])->latest()->take(20);
        }]);

        return view('admin.error-codes.show', compact('errorCode'));
    }

    /**
     * Hata kodu düzenleme formu
     */
    public function edit(ErrorCode $errorCode)
    {
        $categories = ErrorCode::getCategories();
        return view('admin.error-codes.edit', compact('errorCode', 'categories'));
    }

    /**
     * Hata kodu güncelle
     */
    public function update(Request $request, ErrorCode $errorCode)
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:50', 'unique:error_codes,code,' . $errorCode->id],
            'category' => ['required', 'string', 'max:100'],
            'name' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string'],
            'is_active' => ['boolean'],
        ]);

        $oldValues = $errorCode->toArray();
        $errorCode->update($validated);

        // Activity log
        ActivityLog::createLog(
            userId: auth()->id(),
            action: 'update',
            description: 'Hata kodu güncellendi: ' . $errorCode->code,
            modelType: 'ErrorCode',
            modelId: $errorCode->id,
            oldValues: $oldValues,
            newValues: $errorCode->fresh()->toArray()
        );

        return redirect()
            ->route('admin.error-codes.index')
            ->with('success', 'Hata kodu başarıyla güncellendi.');
    }

    /**
     * Hata kodunu sil (soft delete)
     */
    public function destroy(ErrorCode $errorCode)
    {
        $errorCodeName = $errorCode->code;
        $errorCode->delete();

        // Activity log
        ActivityLog::createLog(
            userId: auth()->id(),
            action: 'delete',
            description: 'Hata kodu silindi: ' . $errorCodeName,
            modelType: 'ErrorCode',
            modelId: $errorCode->id
        );

        return redirect()
            ->route('admin.error-codes.index')
            ->with('success', 'Hata kodu başarıyla silindi.');
    }
}
