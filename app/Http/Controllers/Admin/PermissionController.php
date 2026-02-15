<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

/**
 * Permission Controller - Yetki Yönetimi
 * 
 * Sadece admin erişebilir
 * Roller ve permission'ların yönetimi
 */
class PermissionController extends Controller
{
    /**
     * Tüm rolleri ve permission'ları listele
     */
    public function index()
    {
        $roles = Role::with('permissions')->orderBy('name')->get();
        $permissions = Permission::orderBy('name')->get();

        // Permission'ları kategorilere göre grupla
        $permissionsByCategory = [
            'Duruş Yönetimi' => $permissions->filter(fn($p) => str_contains($p->name, 'downtime')),
            'Raporlar' => $permissions->filter(fn($p) => str_contains($p->name, 'report')),
            'Makine Yönetimi' => $permissions->filter(fn($p) => str_contains($p->name, 'machine')),
            'Hata Kodu Yönetimi' => $permissions->filter(fn($p) => str_contains($p->name, 'error-code')),
            'Kullanıcı Yönetimi' => $permissions->filter(fn($p) => str_contains($p->name, 'user')),
            'Sistem' => $permissions->filter(fn($p) => str_contains($p->name, 'activity-log') || str_contains($p->name, 'permission')),
        ];

        return view('admin.permissions.index', compact('roles', 'permissionsByCategory'));
    }

    /**
     * Rol için permission düzenleme formu
     */
    public function edit(Role $role)
    {
        $permissions = Permission::orderBy('name')->get();

        // Rol'ün mevcut permission'ları
        $rolePermissions = $role->permissions->pluck('id')->toArray();

        // Permission'ları kategorilere göre grupla
        $permissionsByCategory = [
            'Duruş Yönetimi' => $permissions->filter(fn($p) => str_contains($p->name, 'downtime')),
            'Raporlar' => $permissions->filter(fn($p) => str_contains($p->name, 'report')),
            'Makine Yönetimi' => $permissions->filter(fn($p) => str_contains($p->name, 'machine')),
            'Hata Kodu Yönetimi' => $permissions->filter(fn($p) => str_contains($p->name, 'error-code')),
            'Kullanıcı Yönetimi' => $permissions->filter(fn($p) => str_contains($p->name, 'user')),
            'Sistem' => $permissions->filter(fn($p) => str_contains($p->name, 'activity-log') || str_contains($p->name, 'permission')),
        ];

        return view('admin.permissions.edit', compact('role', 'permissionsByCategory', 'rolePermissions'));
    }

    /**
     * Rol permission'larını güncelle
     */
    public function update(Request $request, Role $role)
    {
        $validated = $request->validate([
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['exists:permissions,id'],
        ]);

        $oldPermissions = $role->permissions->pluck('name')->toArray();

        // Permission ID'lerini Permission modellerine dönüştür
        $permissionIds = $validated['permissions'] ?? [];
        $permissions = Permission::whereIn('id', $permissionIds)->get();

        // Permission'ları senkronize et
        $role->syncPermissions($permissions);

        $newPermissions = $role->fresh()->permissions->pluck('name')->toArray();

        // Activity log
        ActivityLog::createLog(
            userId: auth()->id(),
            action: 'update',
            description: "'{$role->name}' rolünün yetkileri güncellendi",
            modelType: 'Role',
            modelId: $role->id,
            oldValues: ['permissions' => $oldPermissions],
            newValues: ['permissions' => $newPermissions]
        );

        return redirect()
            ->route('admin.permissions.index')
            ->with('success', "'{$role->name}' rolü için yetkiler başarıyla güncellendi.");
    }
}
