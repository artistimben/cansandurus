@extends('layouts.app')

@section('title', 'Makine Yönetimi')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <h1 class="text-3xl font-bold text-gray-900">Makine Yönetimi</h1>
        <a href="{{ route('admin.machines.create') }}" class="btn btn-primary">
            + Yeni Makine Ekle
        </a>
    </div>

    <div class="card">
        <div class="overflow-x-auto">
            <table class="table">
                <thead>
                    <tr>
                        <th>Kod</th>
                        <th>Ad</th>
                        <th>Lokasyon</th>
                        <th>Duruş Sayısı</th>
                        <th>Durum</th>
                        <th>İşlemler</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($machines as $machine)
                    <tr>
                        <td class="font-bold">{{ $machine->code }}</td>
                        <td>{{ $machine->name }}</td>
                        <td>{{ $machine->location }}</td>
                        <td>{{ $machine->downtime_records_count }}</td>
                        <td>
                            @if($machine->is_active)
                            <span class="badge badge-green">Aktif</span>
                            @else
                            <span class="badge badge-red">Pasif</span>
                            @endif
                        </td>
                        <td class="space-x-2">
                            <a href="{{ route('admin.machines.edit', $machine) }}" class="btn btn-secondary text-sm">
                                Düzenle
                            </a>
                            <form method="POST" action="{{ route('admin.machines.destroy', $machine) }}" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger text-sm" onclick="return confirm('Bu makineyi silmek istediğinize emin misiniz?')">
                                    Sil
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <div class="mt-4">
            {{ $machines->links() }}
        </div>
    </div>
</div>
@endsection
