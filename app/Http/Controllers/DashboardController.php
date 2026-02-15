<?php

namespace App\Http\Controllers;

use App\Models\DowntimeRecord;
use App\Models\Machine;
use App\Models\ErrorCode;
use Carbon\Carbon;
use Illuminate\Http\Request;

/**
 * Dashboard Controller - Ana Sayfa
 * 
 * Özet bilgiler ve aktif duruşları gösterir
 */
class DashboardController extends Controller
{
    /**
     * Dashboard sayfasını göster
     */
    public function index()
    {
        $user = auth()->user();

        // Bugünkü duruşlar
        $todayDowntimes = DowntimeRecord::with(['machine', 'errorCode', 'startedBy'])
            ->whereDate('started_at', today())
            ->latest()
            ->take(10)
            ->get();

        // Aktif (devam eden) duruşlar
        $activeDowntimes = DowntimeRecord::with(['machine', 'errorCode', 'startedBy'])
            ->active()
            ->latest()
            ->get();

        // İstatistikler
        $stats = [
            'total_machines' => Machine::active()->count(),
            'active_downtimes' => $activeDowntimes->count(),
            'today_total_downtime' => DowntimeRecord::completed()
                ->whereDate('started_at', today())
                ->sum('duration_minutes'),
            'today_downtime_count' => DowntimeRecord::whereDate('started_at', today())->count(),
        ];

        // Bu haftanın duruş trendi (son 7 gün)
        $weeklyTrend = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $weeklyTrend[] = [
                'date' => $date->format('d/m'),
                'count' => DowntimeRecord::whereDate('started_at', $date)->count(),
                'duration' => DowntimeRecord::completed()
                    ->whereDate('started_at', $date)
                    ->sum('duration_minutes'),
            ];
        }

        // En çok duruş yapan makineler (bugün)
        $topMachinesToday = DowntimeRecord::with('machine')
            ->whereDate('started_at', today())
            ->select('machine_id')
            ->selectRaw('COUNT(*) as downtime_count')
            ->selectRaw('SUM(duration_minutes) as total_duration')
            ->groupBy('machine_id')
            ->orderByDesc('downtime_count')
            ->take(5)
            ->get();

        return view('dashboard', compact(
            'user',
            'todayDowntimes',
            'activeDowntimes',
            'stats',
            'weeklyTrend',
            'topMachinesToday'
        ));
    }
}
