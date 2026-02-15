@extends('layouts.app')

@section('title', 'Aktivite Logları')

@section('content')
<div class="container">
    <div class="mb-4">
        <a href="{{ route('admin.machines.index') }}" class="text-blue-600" style="text-decoration: none;">← Yönetim Paneli</a>
    </div>

    <div class="card">
        <h1 class="text-2xl font-bold mb-6">Aktivite Logları</h1>
        
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Tarih</th>
                        <th>Kullanıcı</th>
                        <th>İşlem</th>
                        <th>Açıklama</th>
                        <th>IP Adresi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($logs as $log)
                    <tr>
                        <td>{{ $log->created_at->format('d/m/Y H:i:s') }}</td>
                        <td>{{ $log->user ? $log->user->name : 'Sistem' }}</td>
                        <td>
                            <span class="badge 
                                @if($log->action === 'create') badge-green
                                @elseif($log->action === 'update') badge-blue
                                @elseif($log->action === 'delete') badge-red
                                @else badge-gray
                                @endif
                            ">
                                {{ ucfirst($log->action) }}
                            </span>
                        </td>
                        <td>{{ $log->description }}</td>
                        <td class="text-sm text-gray-600">{{ $log->ip_address }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <div class="mt-4">
            {{ $logs->links() }}
        </div>
    </div>
</div>
@endsection
