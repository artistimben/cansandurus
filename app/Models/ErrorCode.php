<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * ErrorCode Model - Hata Kodu Modeli
 * 
 * Duruş sebeplerini kategorize eder (Mekanik, Elektrik, Kalite vb.)
 * İlişkiler: downtimeRecords
 */
class ErrorCode extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'code',
        'category',
        'name',
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
     * Hata kodunun duruş kayıtları
     */
    public function downtimeRecords()
    {
        return $this->hasMany(DowntimeRecord::class);
    }

    /**
     * Hata kodu aktif mi?
     */
    public function isActive(): bool
    {
        return $this->is_active;
    }

    /**
     * Scope: Sadece aktif hata kodları
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: Kategoriye göre filtrele
     */
    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Tüm kategorileri getir
     */
    public static function getCategories(): array
    {
        return self::distinct()->pluck('category')->toArray();
    }
}
