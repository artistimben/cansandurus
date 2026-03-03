<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * FaultReport Model - Duruşsuz Arıza Kayıt Modeli
 *
 * Ocakları durdurmadan gerçekleşen arızaları kaydeder.
 * Bu kayıtlar duruş sürelerine dahil edilmez.
 * İlişkiler: machine, errorCode, reportedBy, resolvedBy
 */
class FaultReport extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'machine_id',
        'error_code_id',
        'reported_by',
        'resolved_by',
        'reported_at',
        'resolved_at',
        'title',
        'description',
        'action_taken',
        'priority',
        'status',
        'production_continued',
    ];

    protected $casts = [
        'reported_at' => 'datetime',
        'resolved_at' => 'datetime',
        'production_continued' => 'boolean',
    ];

    // ─── Öncelik etiketleri ────────────────────────────────────────────────
    public static array $priorities = [
        'low' => ['label' => 'Düşük', 'color' => 'badge-gray'],
        'medium' => ['label' => 'Orta', 'color' => 'badge-blue'],
        'high' => ['label' => 'Yüksek', 'color' => 'badge-orange'],
        'critical' => ['label' => 'Kritik', 'color' => 'badge-red'],
    ];

    // ─── Durum etiketleri ─────────────────────────────────────────────────
    public static array $statuses = [
        'open' => ['label' => 'Açık', 'color' => 'badge-red'],
        'in_progress' => ['label' => 'İşlemde', 'color' => 'badge-orange'],
        'resolved' => ['label' => 'Çözüldü', 'color' => 'badge-green'],
    ];

    // ─── İlişkiler ────────────────────────────────────────────────────────

    public function machine()
    {
        return $this->belongsTo(Machine::class);
    }

    public function errorCode()
    {
        return $this->belongsTo(ErrorCode::class);
    }

    public function reportedBy()
    {
        return $this->belongsTo(User::class, 'reported_by');
    }

    public function resolvedBy()
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }

    // ─── Helper metodlar ──────────────────────────────────────────────────

    public function isOpen(): bool
    {
        return $this->status === 'open';
    }

    public function isResolved(): bool
    {
        return $this->status === 'resolved';
    }

    public function getPriorityLabel(): string
    {
        return self::$priorities[$this->priority]['label'] ?? $this->priority;
    }

    public function getPriorityColor(): string
    {
        return self::$priorities[$this->priority]['color'] ?? 'badge-gray';
    }

    public function getStatusLabel(): string
    {
        return self::$statuses[$this->status]['label'] ?? $this->status;
    }

    public function getStatusColor(): string
    {
        return self::$statuses[$this->status]['color'] ?? 'badge-gray';
    }

    /** Arızanın çözüm süresini dakika cinsinden döndürür */
    public function getResolutionMinutes(): ?int
    {
        if (!$this->resolved_at || !$this->reported_at) {
            return null;
        }
        return $this->reported_at->diffInMinutes($this->resolved_at);
    }

    // ─── Scope'lar ────────────────────────────────────────────────────────

    public function scopeOpen($query)
    {
        return $query->where('status', 'open');
    }

    public function scopeResolved($query)
    {
        return $query->where('status', 'resolved');
    }

    public function scopeByPriority($query, string $priority)
    {
        return $query->where('priority', $priority);
    }

    public function scopeForMachine($query, int $machineId)
    {
        return $query->where('machine_id', $machineId);
    }

    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('reported_at', [$startDate, $endDate]);
    }
}
