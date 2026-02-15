<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

/**
 * User Controller - Kullanıcı Yönetimi
 * 
 * Sadece admin erişebilir
 */
class UserController extends Controller
{
    /**
     * Kullanıcıları listele
     */
    public function index()
    {
        $users = User::withCount(['downtimeRecordsStarted', 'activityLogs'])
            ->orderBy('role')
            ->orderBy('name')
            ->paginate(20);

        return view('admin.users.index', compact('users'));
    }

    /**
     * Yeni kullanıcı oluşturma formu
     */
    public function create()
    {
        $roles = ['admin', 'manager', 'operator', 'maintenance'];
        return view('admin.users.create', compact('roles'));
    }

    /**
     * Yeni kullanıcı kaydet
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Password::min(12)
                ->mixedCase()
                ->numbers()
                ->symbols()],
            'role' => ['required', 'in:admin,manager,operator,maintenance'],
            'is_active' => ['boolean'],
        ], [
            'name.required' => 'İsim zorunludur.',
            'email.required' => 'Email zorunludur.',
            'email.unique' => 'Bu email zaten kullanılıyor.',
            'password.required' => 'Şifre zorunludur.',
            'password.min' => 'Şifre en az 12 karakter olmalıdır.',
            'role.required' => 'Rol zorunludur.',
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $user = User::create($validated);

        // Rol ata
        $user->assignRole($validated['role']);

        // Activity log
        ActivityLog::createLog(
            userId: auth()->id(),
            action: 'create',
            description: 'Yeni kullanıcı oluşturuldu: ' . $user->name,
            modelType: 'User',
            modelId: $user->id
        );

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Kullanıcı başarıyla oluşturuldu.');
    }

    /**
     * Kullanıcı detayını göster
     */
    public function show(User $user)
    {
        $user->load(['downtimeRecordsStarted' => function ($query) {
            $query->with(['machine', 'errorCode'])->latest()->take(20);
        }, 'activityLogs' => function ($query) {
            $query->latest()->take(20);
        }]);

        return view('admin.users.show', compact('user'));
    }

    /**
     * Kullanıcı düzenleme formu
     */
    public function edit(User $user)
    {
        $roles = ['admin', 'manager', 'operator', 'maintenance'];
        return view('admin.users.edit', compact('user', 'roles'));
    }

    /**
     * Kullanıcı güncelle
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'password' => ['nullable', 'confirmed', Password::min(12)
                ->mixedCase()
                ->numbers()
                ->symbols()],
            'role' => ['required', 'in:admin,manager,operator,maintenance'],
            'is_active' => ['boolean'],
        ]);

        // Şifre varsa güncelle
        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $oldRole = $user->role;
        $user->update($validated);

        // Rol değiştiyse güncelle
        if ($oldRole !== $validated['role']) {
            $user->syncRoles([$validated['role']]);
        }

        // Activity log
        ActivityLog::createLog(
            userId: auth()->id(),
            action: 'update',
            description: 'Kullanıcı güncellendi: ' . $user->name,
            modelType: 'User',
            modelId: $user->id
        );

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Kullanıcı başarıyla güncellendi.');
    }

    /**
     * Kullanıcıyı sil (soft delete)
     */
    public function destroy(User $user)
    {
        // Kendini silmesini engelle
        if ($user->id === auth()->id()) {
            return back()->withErrors([
                'error' => 'Kendi hesabınızı silemezsiniz.'
            ]);
        }

        $userName = $user->name;
        $user->delete();

        // Activity log
        ActivityLog::createLog(
            userId: auth()->id(),
            action: 'delete',
            description: 'Kullanıcı silindi: ' . $userName,
            modelType: 'User',
            modelId: $user->id
        );

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Kullanıcı başarıyla silindi.');
    }
}
