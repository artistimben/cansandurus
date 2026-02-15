@extends('layouts.app')

@section('title', 'Kullanıcı Yönetimi')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <h1 class="text-3xl font-bold text-gray-900">Kullanıcı Yönetimi</h1>
        <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
            + Yeni Kullanıcı Ekle
        </a>
    </div>

    <div class="card">
        <div class="overflow-x-auto">
            <table class="table">
                <thead>
                    <tr>
                        <th>Ad</th>
                        <th>Email</th>
                        <th>Rol</th>
                        <th>Son Giriş</th>
                        <th>Durum</th>
                        <th>İşlemler</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                    <tr>
                        <td class="font-medium">{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>
                            <span class="badge badge-blue">{{ ucfirst($user->role) }}</span>
                        </td>
                        <td>
                            @if($user->last_login_at)
                            {{ $user->last_login_at->diffForHumans() }}
                            @else
                            <span class="text-gray-400">Hiç giriş yapmadı</span>
                            @endif
                        </td>
                        <td>
                            @if($user->is_active)
                            <span class="badge badge-green">Aktif</span>
                            @else
                            <span class="badge badge-red">Pasif</span>
                            @endif
                        </td>
                        <td class="space-x-2">
                            <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-secondary text-sm">
                                Düzenle
                            </a>
                            @if($user->id !== auth()->id())
                            <form method="POST" action="{{ route('admin.users.destroy', $user) }}" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger text-sm" onclick="return confirm('Bu kullanıcıyı silmek istediğinize emin misiniz?')">
                                    Sil
                                </button>
                            </form>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <div class="mt-4">
            {{ $users->links() }}
        </div>
    </div>
</div>
@endsection
