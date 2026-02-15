@extends('layouts.app')

@section('title', 'Yönetim Paneli')

@section('content')
    <div class="max-w-7xl mx-auto">
        <h1 class="text-3xl font-bold text-gray-900 mb-6">Yönetim Paneli</h1>

        <!-- Admin Navigation Tabs -->
        <div class="bg-white rounded-lg shadow-md mb-6">
            <div class="border-b border-gray-200">
                <nav class="-mb-px flex space-x-8 px-6" aria-label="Tabs">
                    <a href="{{ route('admin.machines.index') }}"
                        class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm {{ request()->routeIs('admin.machines.*') ? 'border-accent-500 text-accent-600' : '' }}">
                        🏭 Makineler
                    </a>
                    <a href="{{ route('admin.error-codes.index') }}"
                        class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm {{ request()->routeIs('admin.error-codes.*') ? 'border-accent-500 text-accent-600' : '' }}">
                        ⚠️ Hata Kodları
                    </a>
                    <a href="{{ route('admin.users.index') }}"
                        class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm {{ request()->routeIs('admin.users.*') ? 'border-accent-500 text-accent-600' : '' }}">
                        👥 Kullanıcılar
                    </a>
                    <a href="{{ route('admin.permissions.index') }}"
                        class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm {{ request()->routeIs('admin.permissions.*') ? 'border-accent-500 text-accent-600' : '' }}">
                        🔐 Yetki Yönetimi
                    </a>
                    <a href="{{ route('admin.activity-logs') }}"
                        class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm {{ request()->routeIs('admin.activity-logs') ? 'border-accent-500 text-accent-600' : '' }}">
                        📋 Aktivite Logları
                    </a>
                </nav>
            </div>
        </div>

        <!-- Content Area -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <a href="{{ route('admin.machines.index') }}" class="card hover:shadow-lg transition-shadow">
                <div class="text-center">
                    <div class="text-4xl mb-3">🏭</div>
                    <h3 class="text-lg font-bold text-gray-900 mb-2">Makineler</h3>
                    <p class="text-sm text-gray-600">Makine ekle, düzenle, yönet</p>
                </div>
            </a>

            <a href="{{ route('admin.error-codes.index') }}" class="card hover:shadow-lg transition-shadow">
                <div class="text-center">
                    <div class="text-4xl mb-3">⚠️</div>
                    <h3 class="text-lg font-bold text-gray-900 mb-2">Hata Kodları</h3>
                    <p class="text-sm text-gray-600">Hata kod tanımları</p>
                </div>
            </a>

            <a href="{{ route('admin.users.index') }}" class="card hover:shadow-lg transition-shadow">
                <div class="text-center">
                    <div class="text-4xl mb-3">👥</div>
                    <h3 class="text-lg font-bold text-gray-900 mb-2">Kullanıcılar</h3>
                    <p class="text-sm text-gray-600">Kullanıcı yönetimi</p>
                </div>
            </a>

            <a href="{{ route('admin.permissions.index') }}"
                class="card hover:shadow-lg transition-shadow border-2 border-accent-500">
                <div class="text-center">
                    <div class="text-4xl mb-3">🔐</div>
                    <h3 class="text-lg font-bold text-accent-900 mb-2">Yetki Yönetimi</h3>
                    <p class="text-sm text-accent-700 font-medium">Rol ve yetki ayarları</p>
                    <span class="badge badge-accent mt-2">YENİ</span>
                </div>
            </a>

            <a href="{{ route('admin.activity-logs') }}"
                class="card hover:shadow-lg transition-shadow md:col-span-2 lg:col-span-4">
                <div class="text-center">
                    <div class="text-4xl mb-3">📋</div>
                    <h3 class="text-lg font-bold text-gray-900 mb-2">Aktivite Logları</h3>
                    <p class="text-sm text-gray-600">Sistem aktivitelerini görüntüle</p>
                </div>
            </a>
        </div>
    </div>
@endsection