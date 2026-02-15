<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

/**
 * DowntimeRecord Model - Duruş Kayıt Modeli
 * 
 * Makine duruşlarını kaydeder
 * İlişkiler: machine, errorCode, startedBy, endedBy
 */
class DowntimeRecord extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'machine_id',
        'error_code_id',
        'started_by',
        'ended_by',
        'started_at',
        'ended_at',
        'duration_minutes',
        'notes',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
        'duration_minutes' => 'integer',
    ];

    /**
     * İlgili makine
     */
    public function machine()
    {
        return $this->belongsTo(Machine::class);
    }

    /**
     * İlgili hata kodu
     */
    public function errorCode()
    {
        return $this->belongsTo(ErrorCode::class);
    }

    /**
     * Duruşu başlatan kullanıcı
     */
    public function startedBy()
    {
        return $this->belongsTo(User::class, 'started_by');
    }

    /**
     * Duruşu bitiren kullanıcı
     */
    public function endedBy()
    {
        return $this->belongsTo(User::class, 'ended_by');
    }

    /**
     * Duruş aktif mi?
     */
    public function isActive(): bool
    {
        return $this->status === 'active' && $this->ended_at === null;
    }

    /**
     * Duruş tamamlandı mı?
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed' && $this->ended_at !== null;
    }

    /**
     * Duruşu bitir
     */
    public function complete(int $userId): void
    {
        $this->ended_at = now();
        $this->ended_by = $userId;
        $this->duration_minutes = $this->calculateDuration();
        $this->status = 'completed';
        $this->save();
    }

    /**
     * Duruş süresini hesapla (dakika cinsinden)
     */
    public function calculateDuration(): int
    {
        if (!$this->ended_at) {
            return 0;
        }

        return Carbon::parse($this->started_at)->diffInMinutes(Carbon::parse($this->ended_at));
    }

    /**
     * Scope: Aktif duruşlar
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active')->whereNull('ended_at');
    }

    /**
     * Scope: Tamamlanmış duruşlar
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed')->whereNotNull('ended_at');
    }

    /**
     * Scope: Tarih aralığına göre filtrele
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('started_at', [$startDate, $endDate]);
    }

    /**
     * Scope: Belirli bir makineye göre filtrele
     */
    public function scopeForMachine($query, int $machineId)
    {
        return $query->where('machine_id', $machineId);
    }

    /**
     * Scope: Belirli bir hata koduna göre filtrele
     */
    public function scopeForErrorCode($query, int $errorCodeId)
    {
        return $query->where('error_code_id', $errorCodeId);
    }
}
