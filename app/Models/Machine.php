<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Machine Model - Makine/Hat Modeli
 * 
 * Fabrikadaki makineleri/hatları temsil eder
 * İlişkiler: downtimeRecords
 */
class Machine extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'code',
        'name',
        'location',
        'description',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Makinenin duruş kayıtları
     */
    public function downtimeRecords()
    {
        return $this->hasMany(DowntimeRecord::class);
    }

    /**
     * Makinenin aktif (devam eden) duruş kayıtları
     */
    public function activeDowntimeRecords()
    {
        return $this->hasMany(DowntimeRecord::class)
                    ->where('status', 'active')
                    ->whereNull('ended_at');
    }

    /**
     * Makine aktif mi?
     */
    public function isActive(): bool
    {
        return $this->is_active;
    }

    /**
     * Scope: Sadece aktif makineler
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
