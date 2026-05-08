@extends('layouts.app')

@section('title', 'Arıza Bildirimleri')

@section('content')
    <div class="max-w-7xl mx-auto space-y-6">

        {{-- Başlık --}}
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">🔧 Arıza Bildirimleri</h1>
                <p class="text-gray-600 mt-1">Ocakları durdurmayan arızaların kayıtları</p>
            </div>
            <a href="{{ route('fault-reports.create') }}" class="btn btn-accent">
                ⚠️ Yeni Arıza Bildir
            </a>
        </div>

        {{-- Özet Kartlar --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="card bg-red-50 border-red-200 text-center py-4">
                <div class="text-3xl font-bold text-red-600">{{ $stats['open'] }}</div>
                <div class="text-sm text-red-700 font-medium mt-1">Açık Arıza</div>
            </div>
            <div class="card bg-orange-50 border-orange-200 text-center py-4">
                <div class="text-3xl font-bold text-orange-600">{{ $stats['in_progress'] }}</div>
                <div class="text-sm text-orange-700 font-medium mt-1">İşlemde</div>
            </div>
            <div class="card bg-green-50 border-green-200 text-center py-4">
                <div class="text-3xl font-bold text-green-600">{{ $stats['resolved'] }}</div>
                <div class="text-sm text-green-700 font-medium mt-1">Çözüldü</div>
            </div>
            <div class="card bg-purple-50 border-purple-200 text-center py-4">
                <div class="text-3xl font-bold text-purple-600">{{ $stats['critical'] }}</div>
                <div class="text-sm text-purple-700 font-medium mt-1">Kritik Açık</div>
            </div>
        </div>

        {{-- Filtreler --}}
        <div class="card">
            <form method="GET" action="{{ route('fault-reports.index') }}" class="flex flex-wrap gap-3 items-end">
                <div>
                    <label class="block text-xs text-gray-600 mb-1">Durum</label>
                    <select name="status" class="form-select form-select-sm">
                        <option value="">Tümü</option>
                        <option value="open" {{ request('status') === 'open' ? 'selected' : '' }}>Açık</option>
                        <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>İşlemde
                        </option>
                        <option value="resolved" {{ request('status') === 'resolved' ? 'selected' : '' }}>Çözüldü</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs text-gray-600 mb-1">Öncelik</label>
                    <select name="priority" class="form-select form-select-sm">
                        <option value="">Tümü</option>
                        <option value="low" {{ request('priority') === 'low' ? 'selected' : '' }}>Düşük</option>
                        <option value="medium" {{ request('priority') === 'medium' ? 'selected' : '' }}>Orta</option>
                        <option value="high" {{ request('priority') === 'high' ? 'selected' : '' }}>Yüksek</option>
                        <option value="critical" {{ request('priority') === 'critical' ? 'selected' : '' }}>Kritik</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs text-gray-600 mb-1">Makine</label>
                    <select name="machine_id" class="form-select form-select-sm">
                        <option value="">Tümü</option>
                        @foreach($machines as $machine)
                            <option value="{{ $machine->id }}" {{ request('machine_id') == $machine->id ? 'selected' : '' }}>
                                {{ $machine->code }} - {{ $machine->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="btn btn-primary btn-sm">Filtrele</button>
                <a href="{{ route('fault-reports.index') }}" class="btn btn-secondary btn-sm">Temizle</a>
            </form>
        </div>

        {{-- Liste --}}
        <div class="card">
            <div class="overflow-x-auto">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Zaman Aralığı</th>
                            <th>Makine</th>
                            <th>Arıza Başlığı</th>
                            <th>Öncelik</th>
                            <th>Durum</th>
                            <th>Üretim</th>
                            <th>İşlem</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($faultReports as $report)
                            <tr class="{{ $report->priority === 'critical' && !$report->isResolved() ? 'bg-red-50' : '' }}">
                                <td class="whitespace-nowrap">
                                    <div class="flex flex-col">
                                        <div class="flex items-center gap-1 text-sm font-medium text-gray-900">
                                            <span class="text-xs text-gray-400 w-4">🕒</span>
                                            {{ $report->reported_at->format('d.m.Y H:i') }}
                                        </div>
                                        @if($report->resolved_at)
                                            <div class="flex items-center gap-1 text-sm font-medium text-green-600">
                                                <span class="text-xs text-gray-400 w-4">🏁</span>
                                                {{ $report->resolved_at->format('d.m.Y H:i') }}
                                            </div>
                                            @if($report->getResolutionMinutes())
                                                <div class="text-[10px] text-gray-400 ml-5">
                                                    ⏱️ {{ $report->getResolutionMinutes() }} dk sürdü
                                                </div>
                                            @endif
                                        @else
                                            <div class="flex items-center gap-1 text-sm font-medium text-amber-600">
                                                <span class="text-xs text-gray-400 w-4">⏳</span>
                                                Devam Ediyor...
                                            </div>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <span class="badge badge-primary">{{ $report->machine->code ?? 'N/A' }}</span>
                                    <p class="text-xs text-gray-500 mt-1">{{ $report->machine->name ?? '' }}</p>
                                </td>
                                <td>
                                    <p class="font-medium text-sm max-w-xs">{{ $report->title }}</p>
                                    @if($report->errorCode)
                                        <span class="badge badge-gray text-xs mt-1">{{ $report->errorCode->code }}</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge {{ $report->getPriorityColor() }}">
                                        {{ $report->getPriorityLabel() }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge {{ $report->getStatusColor() }}">
                                        {{ $report->getStatusLabel() }}
                                    </span>
                                </td>
                                <td>
                                    @if($report->production_continued)
                                        <span class="text-green-600 font-medium text-sm">✅ Devam etti</span>
                                    @else
                                        <span class="text-orange-600 font-medium text-sm">⚠️ Etkilendi</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="flex gap-2">
                                        <a href="{{ route('fault-reports.show', $report) }}" class="btn btn-secondary btn-sm">
                                            👁️
                                        </a>
                                        @if(!$report->isResolved())
                                            <a href="{{ route('fault-reports.edit', $report) }}" class="btn btn-primary btn-sm">
                                                ✏️
                                            </a>
                                        @endif
                                        @if(in_array(auth()->user()->role, ['admin', 'manager']))
                                            <form method="POST" action="{{ route('fault-reports.destroy', $report) }}"
                                                class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm"
                                                    onclick="return confirm('Bu arıza kaydını kalıcı olarak silmek istediğinize emin misiniz?')">
                                                    🗑️
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-12 text-gray-500">
                                    <div class="text-4xl mb-2">✅</div>
                                    <p>Kayıt bulunamadı.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Sayfalama --}}
            @if($faultReports->hasPages())
                <div class="mt-4">
                    {{ $faultReports->links() }}
                </div>
            @endif
        </div>

    </div>
@endsection