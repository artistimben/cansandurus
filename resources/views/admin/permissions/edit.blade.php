@extends('layouts.app')

@section('title', 'Yetki Düzenleme - ' . ucfirst($role->name))

@section('content')
    <div class="max-w-4xl mx-auto">
        <div class="mb-6">
            <a href="{{ route('admin.permissions.index') }}" class="text-primary-600 hover:text-primary-800">
                ← Geri Dön
            </a>
        </div>

        <div class="card">
            <div class="flex items-center justify-between mb-6">
                <h1 class="text-2xl font-bold text-gray-900">
                    <span class="capitalize">{{ $role->name }}</span> Rolü - Yetki Düzenleme
                </h1>
                <span class="badge badge-primary">{{ count($rolePermissions) }} /
                    {{ collect($permissionsByCategory)->flatten()->count() }} Yetki</span>
            </div>

            <form method="POST" action="{{ route('admin.permissions.update', $role) }}">
                @csrf
                @method('PUT')

                <div class="space-y-6">
                    @foreach($permissionsByCategory as $category => $permissions)
                        @if($permissions->count() > 0)
                            <div class="border border-gray-200 rounded-lg p-5">
                                <div class="flex items-center justify-between mb-4">
                                    <h3 class="text-lg font-semibold text-gray-900">{{ $category }}</h3>
                                    <button type="button" class="text-sm text-accent-600 hover:text-accent-800 font-medium"
                                        onclick="toggleCategory('{{ str_replace(' ', '_', $category) }}')">
                                        Tümünü Seç/Kaldır
                                    </button>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3" id="{{ str_replace(' ', '_', $category) }}">
                                    @foreach($permissions as $permission)
                                        <label
                                            class="flex items-center p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer transition-colors">
                                            <input type="checkbox" name="permissions[]" value="{{ $permission->id }}"
                                                class="w-4 h-4 text-accent-600 rounded focus:ring-accent-500 category-checkbox-{{ str_replace(' ', '_', $category) }}"
                                                {{ in_array($permission->id, $rolePermissions) ? 'checked' : '' }}>
                                            <span class="ml-3 text-sm text-gray-700 font-medium">
                                                {{ str_replace('-', ' ', ucfirst($permission->name)) }}
                                            </span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>

                <!-- Butonlar -->
                <div class="flex justify-end space-x-3 mt-6 pt-6 border-t border-gray-200">
                    <a href="{{ route('admin.permissions.index') }}" class="btn btn-secondary">
                        İptal
                    </a>
                    <button type="submit" class="btn btn-accent">
                        💾 Yetkileri Kaydet
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function toggleCategory(category) {
            const checkboxes = document.querySelectorAll(`.category-checkbox-${category}`);
            const allChecked = Array.from(checkboxes).every(cb => cb.checked);

            checkboxes.forEach(cb => {
                cb.checked = !allChecked;
            });
        }
    </script>
@endsection