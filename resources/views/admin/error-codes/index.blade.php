@extends('layouts.app')

@section('title', 'Hata Kodu Yönetimi')

@section('content')
    <div class="space-y-6">
        <div class="flex justify-between items-center">
            <h1 class="text-3xl font-bold text-gray-900">Hata Kodu Yönetimi</h1>
            <a href="{{ route('admin.error-codes.create') }}" class="btn btn-primary">
                + Yeni Hata Kodu Ekle
            </a>
        </div>

        <div class="card">
            <div class="overflow-x-auto">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Kod</th>
                            <th>Kategori</th>
                            <th>Ad</th>
                            <th>Kullanım Sayısı</th>
                            <th>Durum</th>
                            <th>İşlemler</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($errorCodes as $errorCode)
                            <tr>
                                <td class="font-bold">{{ $errorCode->code }}</td>
                                <td>
                                    <span class="badge badge-blue">{{ $errorCode->category ?? 'N/A' }}</span>
                                </td>
                                <td>{{ $errorCode->name }}</td>
                                <td>{{ $errorCode->downtime_records_count }}</td>
                                <td>
                                    @if($errorCode->is_active)
                                        <span class="badge badge-green">Aktif</span>
                                    @else
                                        <span class="badge badge-red">Pasif</span>
                                    @endif
                                </td>
                                <td class="space-x-2">
                                    <a href="{{ route('admin.error-codes.edit', $errorCode) }}"
                                        class="btn btn-secondary text-sm">
                                        Düzenle
                                    </a>
                                    <form method="POST" action="{{ route('admin.error-codes.destroy', $errorCode) }}"
                                        class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger text-sm"
                                            onclick="return confirm('Bu hata kodunu silmek istediğinize emin misiniz?')">
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
                {{ $errorCodes->links() }}
            </div>
        </div>
    </div>
@endsection