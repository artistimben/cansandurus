@extends('layouts.app')

@section('title', 'Yetki Yönetimi')

@section('content')
    <div class="max-w-6xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-900">Yetki Yönetimi</h1>
        </div>

        <div class="card">
            <h2 class="text-xl font-bold text-gray-900 mb-4">Roller ve Yetkiler</h2>
            <p class="text-gray-600 mb-6">
                Her rol için yetkileri düzenleyebilir ve kullanıcı erişimlerini kontrol edebilirsiniz.
            </p>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                @foreach ($roles as $role)
                    <div class="border border-gray-200 rounded-lg p-5 hover:border-accent-500 transition-colors">
                        <div class="flex items-center justify-between mb-3">
                            <h3 class="text-lg font-bold text-gray-900 capitalize">{{ $role->name }}</h3>
                            @if($role->name === 'admin')
                                <span class="badge badge-accent">Tam Yetki</span>
                            @else
                                <span class="badge badge-gray">{{ $role->permissions->count() }} Yetki</span>
                            @endif
                        </div>

                        <p class="text-sm text-gray-600 mb-4">
                            @if($role->name === 'admin')
                                Sistemdeki tüm yetkilere sahiptir
                            @elseif($role->name === 'manager')
                                Raporlama ve görüntüleme yetkileri
                            @elseif($role->name === 'operator')
                                Duruş başlatma ve bitirme
                            @else
                                Duruş işlemleri
                            @endif
                        </p>

                        <a href="{{ route('admin.permissions.edit', $role) }}" class="btn btn-primary btn-sm w-full">
                            ⚙️ Yetkileri Düzenle
                        </a>
                    </div>
                @endforeach
            </div>

            <!-- Permission Kategorileri Özeti -->
            <div class="mt-8">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Yetki Kategorileri</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($permissionsByCategory as $category => $permissions)
                        <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                            <h4 class="font-semibold text-gray-900 mb-2">{{ $category }}</h4>
                            <p class="text-sm text-gray-600">{{ $permissions->count() }} yetki</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endsection