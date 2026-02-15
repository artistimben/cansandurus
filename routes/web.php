<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DowntimeController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\Admin\MachineController;
use App\Http\Controllers\Admin\ErrorCodeController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\PermissionController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - Cansan Duruş Takip Sistemi
|--------------------------------------------------------------------------
|
| Güvenlik: Tüm route'lar role middleware ile korunmuştur
| Roller: admin, manager, operator, maintenance
|
*/

// Ana sayfa - login'e yönlendir
Route::get('/', function () {
    return redirect()->route('login');
});

// Auth Routes - Giriş/Çıkış
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.post');
});

Route::post('/logout', [LoginController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

// Authenticated Routes
Route::middleware(['auth'])->group(function () {

    // Dashboard - Tüm roller erişebilir
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Downtime Routes - Operator, Maintenance, Admin
    Route::middleware(['role:operator,maintenance,admin'])->group(function () {
        Route::get('/downtime', [DowntimeController::class, 'index'])->name('downtime.index');
        Route::get('/downtime/create', [DowntimeController::class, 'create'])->name('downtime.create');
        Route::post('/downtime', [DowntimeController::class, 'store'])->name('downtime.store');
        Route::get('/downtime/{downtime}', [DowntimeController::class, 'show'])->name('downtime.show');
        Route::get('/downtime/{downtime}/edit', [DowntimeController::class, 'edit'])->name('downtime.edit');
        Route::put('/downtime/{downtime}', [DowntimeController::class, 'update'])->name('downtime.update');
        Route::post('/downtime/{downtime}/complete', [DowntimeController::class, 'complete'])->name('downtime.complete');
        Route::delete('/downtime/{downtime}', [DowntimeController::class, 'destroy'])->name('downtime.destroy');
    });

    // Report Routes - Manager, Admin
    Route::middleware(['role:manager,admin'])->prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('index');
        Route::get('/daily', [ReportController::class, 'daily'])->name('daily');
        Route::get('/monthly', [ReportController::class, 'monthly'])->name('monthly');
        Route::get('/yearly', [ReportController::class, 'yearly'])->name('yearly');
        Route::get('/error-analysis', [ReportController::class, 'errorCodeAnalysis'])->name('error-analysis');
        Route::post('/export/excel', [ReportController::class, 'exportExcel'])->name('export.excel');
        Route::post('/export/pdf', [ReportController::class, 'exportPdf'])->name('export.pdf');
    });

    // Admin Routes - Sadece Admin
    Route::middleware(['role:admin', 'log.activity'])->prefix('admin')->name('admin.')->group(function () {

        // Admin Dashboard
        Route::get('/', function () {
            return view('admin.dashboard');
        })->name('dashboard');

        // Machine Management
        Route::resource('machines', MachineController::class);

        // Error Code Management
        Route::resource('error-codes', ErrorCodeController::class);

        // User Management
        Route::resource('users', UserController::class);

        // Permission Management
        Route::get('/permissions', [PermissionController::class, 'index'])->name('permissions.index');
        Route::get('/permissions/{role}/edit', [PermissionController::class, 'edit'])->name('permissions.edit');
        Route::put('/permissions/{role}', [PermissionController::class, 'update'])->name('permissions.update');
        // Activity Logs (görüntüleme)
        Route::get('/activity-logs', function () {
            $logs = \App\Models\ActivityLog::with('user')
                ->latest()
                ->paginate(50);
            return view('admin.activity-logs', compact('logs'));
        })->name('activity-logs');
    });
});
