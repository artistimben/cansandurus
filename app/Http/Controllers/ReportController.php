<?php

namespace App\Http\Controllers;

use App\Models\DowntimeRecord;
use App\Models\Machine;
use App\Models\ErrorCode;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

/**
 * Report Controller - Raporlama
 * 
 * Günlük, aylık, yıllık raporlar ve export işlemleri
 */
class ReportController extends Controller
{
    /**
     * Rapor ana sayfası
     */
    public function index()
    {
        return view('reports.index');
    }

    /**
     * Günlük rapor
     */
    public function daily(Request $request)
    {
        $date = $request->input('date', today()->format('Y-m-d'));
        $selectedDate = Carbon::parse($date);

        // O günkü duruşlar
        // NOT: Birden fazla güne yayılan duruşları yakalamak için
        // hem o gün başlayanları hem de o gün içinde devam edenleri alıyoruz
        $downtimes = DowntimeRecord::with(['machine', 'errorCode', 'startedBy', 'endedBy'])
            ->where(function ($query) use ($selectedDate) {
                // O gün başlayanlar
                $query->whereDate('started_at', $selectedDate)
                    // VEYA o gün biten/devam edenler
                    ->orWhere(function ($q) use ($selectedDate) {
                    $q->where('started_at', '<', $selectedDate->copy()->endOfDay())
                        ->where(function ($q2) use ($selectedDate) {
                            // Aktif olanlar (hala devam ediyor)
                            $q2->whereNull('ended_at')
                                ->where('started_at', '<=', $selectedDate->copy()->endOfDay())
                                // VEYA o gün veya sonra bitenler
                                ->orWhere(function ($q3) use ($selectedDate) {
                                $q3->whereNotNull('ended_at')
                                    ->whereDate('ended_at', '>=', $selectedDate->startOfDay());
                            });
                        });
                });
            })
            ->orderBy('started_at')
            ->get();

        // Süre hesaplaması: O güne denk gelen kısmı al
        $downtimes = $downtimes->map(function ($downtime) use ($selectedDate) {
            if ($downtime->status === 'active') {
                // Aktif duruşlar için o güne kadar olan süreyi hesapla
                $start = Carbon::parse($downtime->started_at);
                $dayStart = $selectedDate->copy()->startOfDay();
                $dayEnd = $selectedDate->copy()->endOfDay();

                // Eğer duruş seçilen günden önce başladıysa, gün başından itibaren hesapla
                $effectiveStart = $start->greaterThan($dayStart) ? $start : $dayStart;

                // Şu ana kadar olan süre (ama gün sonunu geçmesin)
                $now = now();
                $effectiveEnd = $now->lessThan($dayEnd) ? $now : $dayEnd;

                $downtime->daily_duration = $effectiveStart->diffInMinutes($effectiveEnd);
            } else {
                // Tamamlanmış duruşlar için o güne denk gelen kısmı hesapla
                $start = Carbon::parse($downtime->started_at);
                $end = Carbon::parse($downtime->ended_at);
                $dayStart = $selectedDate->copy()->startOfDay();
                $dayEnd = $selectedDate->copy()->endOfDay();

                // Kesişim hesapla
                $effectiveStart = $start->greaterThan($dayStart) ? $start : $dayStart;
                $effectiveEnd = $end->lessThan($dayEnd) ? $end : $dayEnd;

                // Eğer bu duruş bu günde varsa
                if ($effectiveStart->lessThanOrEqualTo($effectiveEnd)) {
                    $downtime->daily_duration = $effectiveStart->diffInMinutes($effectiveEnd);
                } else {
                    $downtime->daily_duration = 0;
                }
            }

            return $downtime;
        });

        // İstatistikler - günlük süreleri kullan
        $stats = [
            'total_count' => $downtimes->count(),
            'total_duration' => $downtimes->sum('daily_duration'),
            'active_count' => $downtimes->where('status', 'active')->count(),
            'completed_count' => $downtimes->where('status', 'completed')->count(),
        ];

        // Makine bazında breakdown - günlük süreler
        $byMachine = $downtimes->groupBy('machine_id')->map(function ($items) {
            return [
                'machine' => $items->first()->machine,
                'count' => $items->count(),
                'duration' => $items->sum('daily_duration'),
            ];
        })->sortByDesc('duration');

        // Hata kodu bazında breakdown - günlük süreler
        $byErrorCode = $downtimes->groupBy('error_code_id')->map(function ($items) {
            return [
                'error_code' => $items->first()->errorCode,
                'count' => $items->count(),
                'duration' => $items->sum('daily_duration'),
            ];
        })->sortByDesc('count');

        return view('reports.daily', compact('selectedDate', 'downtimes', 'stats', 'byMachine', 'byErrorCode'));
    }

    /**
     * Aylık rapor
     */
    public function monthly(Request $request)
    {
        $month = $request->input('month', now()->format('Y-m'));
        $selectedMonth = Carbon::parse($month . '-01');

        $startDate = $selectedMonth->copy()->startOfMonth();
        $endDate = $selectedMonth->copy()->endOfMonth();

        // O ayki duruşlar
        $downtimes = DowntimeRecord::with(['machine', 'errorCode'])
            ->whereBetween('started_at', [$startDate, $endDate])
            ->get();

        // İstatistikler
        $stats = [
            'total_count' => $downtimes->count(),
            'total_duration' => $downtimes->where('status', 'completed')->sum('duration_minutes'),
            'avg_duration' => $downtimes->where('status', 'completed')->avg('duration_minutes'),
        ];

        // Günlük trend (her gün için)
        $dailyTrend = [];
        $currentDate = $startDate->copy();
        while ($currentDate <= $endDate) {
            $dayDowntimes = $downtimes->filter(function ($dt) use ($currentDate) {
                return Carbon::parse($dt->started_at)->isSameDay($currentDate);
            });

            $dailyTrend[] = [
                'date' => $currentDate->format('d/m'),
                'count' => $dayDowntimes->count(),
                'duration' => $dayDowntimes->where('status', 'completed')->sum('duration_minutes'),
            ];

            $currentDate->addDay();
        }

        // Makine bazında özet
        $byMachine = $downtimes->groupBy('machine_id')->map(function ($items) {
            return [
                'machine' => $items->first()->machine,
                'count' => $items->count(),
                'duration' => $items->where('status', 'completed')->sum('duration_minutes'),
            ];
        })->sortByDesc('duration');

        // Kategori bazında özet
        $byCategory = $downtimes->groupBy(function ($item) {
            return $item->errorCode->category ?? 'Tanımsız';
        })->map(function ($items, $category) {
            return [
                'category' => $category,
                'count' => $items->count(),
                'duration' => $items->where('status', 'completed')->sum('duration_minutes'),
            ];
        })->sortByDesc('count');

        return view('reports.monthly', compact('selectedMonth', 'stats', 'dailyTrend', 'byMachine', 'byCategory'));
    }

    /**
     * Yıllık rapor
     */
    public function yearly(Request $request)
    {
        $year = $request->input('year', now()->year);
        $selectedYear = Carbon::parse($year . '-01-01');

        $startDate = $selectedYear->copy()->startOfYear();
        $endDate = $selectedYear->copy()->endOfYear();

        // O yılki duruşlar
        $downtimes = DowntimeRecord::with(['machine', 'errorCode'])
            ->whereBetween('started_at', [$startDate, $endDate])
            ->get();

        // İstatistikler
        $stats = [
            'total_count' => $downtimes->count(),
            'total_duration' => $downtimes->where('status', 'completed')->sum('duration_minutes'),
            'avg_monthly_duration' => $downtimes->where('status', 'completed')->sum('duration_minutes') / 12,
        ];

        // Aylık trend
        $monthlyTrend = [];
        for ($month = 1; $month <= 12; $month++) {
            $monthDowntimes = $downtimes->filter(function ($dt) use ($month) {
                return Carbon::parse($dt->started_at)->month === $month;
            });

            $monthlyTrend[] = [
                'month' => Carbon::create(null, $month)->translatedFormat('F'),
                'count' => $monthDowntimes->count(),
                'duration' => $monthDowntimes->where('status', 'completed')->sum('duration_minutes'),
            ];
        }

        // En problemli makineler (tüm yıl)
        $topMachines = $downtimes->groupBy('machine_id')->map(function ($items) {
            return [
                'machine' => $items->first()->machine,
                'count' => $items->count(),
                'duration' => $items->where('status', 'completed')->sum('duration_minutes'),
            ];
        })->sortByDesc('duration')->take(10);

        // Hata kodu dağılımı
        $errorCodeDistribution = $downtimes->groupBy('error_code_id')->map(function ($items) {
            return [
                'error_code' => $items->first()->errorCode,
                'count' => $items->count(),
                'percentage' => 0, // Hesaplanacak
            ];
        })->sortByDesc('count')->take(10);

        $totalCount = $downtimes->count();
        $errorCodeDistribution = $errorCodeDistribution->map(function ($item) use ($totalCount) {
            $item['percentage'] = $totalCount > 0 ? round(($item['count'] / $totalCount) * 100, 1) : 0;
            return $item;
        });

        return view('reports.yearly', compact('selectedYear', 'stats', 'monthlyTrend', 'topMachines', 'errorCodeDistribution'));
    }

    /**
     * Hata kodu bazlı analiz raporu
     */
    public function errorCodeAnalysis(Request $request)
    {
        $errorCodeId = $request->input('error_code_id');
        $startDate = $request->input('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->format('Y-m-d'));
        $machineId = $request->input('machine_id');

        // Tüm hata kodları ve makineler (filtre için)
        $errorCodes = ErrorCode::orderBy('code')->get();
        $machines = Machine::orderBy('code')->get();

        // Eğer hata kodu seçilmemişse boş sonuç
        if (!$errorCodeId) {
            return view('reports.error-analysis', compact('errorCodes', 'machines'));
        }

        $selectedErrorCode = ErrorCode::findOrFail($errorCodeId);
        $selectedStartDate = Carbon::parse($startDate);
        $selectedEndDate = Carbon::parse($endDate);

        // Filtrelenmiş duruşlar
        $query = DowntimeRecord::with(['machine', 'startedBy', 'endedBy'])
            ->where('error_code_id', $errorCodeId)
            ->whereBetween('started_at', [$selectedStartDate->startOfDay(), $selectedEndDate->endOfDay()]);

        if ($machineId) {
            $query->where('machine_id', $machineId);
        }

        $downtimes = $query->orderBy('started_at')->get();

        // Günlük dağılım
        $dailyBreakdown = [];
        $currentDate = $selectedStartDate->copy();

        while ($currentDate <= $selectedEndDate) {
            $dayDowntimes = $downtimes->filter(function ($dt) use ($currentDate) {
                return Carbon::parse($dt->started_at)->isSameDay($currentDate);
            });

            $dailyBreakdown[] = [
                'date' => $currentDate->format('Y-m-d'),
                'date_formatted' => $currentDate->translatedFormat('d F Y, l'),
                'count' => $dayDowntimes->count(),
                'duration' => $dayDowntimes->where('status', 'completed')->sum('duration_minutes'),
                'active_count' => $dayDowntimes->where('status', 'active')->count(),
                'machines' => $dayDowntimes->pluck('machine')->unique('id')->take(3),
            ];

            $currentDate->addDay();
        }

        // İstatistikler
        $stats = [
            'total_count' => $downtimes->count(),
            'total_duration' => $downtimes->where('status', 'completed')->sum('duration_minutes'),
            'avg_duration' => $downtimes->where('status', 'completed')->avg('duration_minutes'),
            'affected_machines' => $downtimes->pluck('machine_id')->unique()->count(),
            'days_with_downtime' => collect($dailyBreakdown)->where('count', '>', 0)->count(),
        ];

        // Makine bazında dağılım
        $byMachine = $downtimes->groupBy('machine_id')->map(function ($items) {
            return [
                'machine' => $items->first()->machine,
                'count' => $items->count(),
                'duration' => $items->where('status', 'completed')->sum('duration_minutes'),
            ];
        })->sortByDesc('duration');

        return view('reports.error-analysis', compact(
            'errorCodes',
            'machines',
            'selectedErrorCode',
            'selectedStartDate',
            'selectedEndDate',
            'stats',
            'dailyBreakdown',
            'byMachine',
            'downtimes'
        ));
    }

    /**
     * Excel export
     */
    public function exportExcel(Request $request)
    {
        // Export logic will be implemented
        return back()->with('info', 'Excel export özelliği yakında eklenecek.');
    }

    /**
     * PDF export
     */
    public function exportPdf(Request $request)
    {
        // PDF export logic will be implemented
        return back()->with('info', 'PDF export özelliği yakında eklenecek.');
    }
}
