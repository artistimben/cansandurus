<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

/**
 * User Model - Kullanıcı Modeli
 * 
 * Roller: admin, manager, operator, maintenance
 * İlişkiler: downtimeRecordsStarted, downtimeRecordsEnded, activityLogs
 */
class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'is_active',
        'last_login_at',
        'last_login_ip',
        'google2fa_enabled',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'google2fa_secret',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'google2fa_enabled' => 'boolean',
            'last_login_at' => 'datetime',
        ];
    }

    /**
     * Kullanıcının başlattığı duruş kayıtları
     */
    public function downtimeRecordsStarted()
    {
        return $this->hasMany(DowntimeRecord::class, 'started_by');
    }

    /**
     * Kullanıcının bitirdiği duruş kayıtları
     */
    public function downtimeRecordsEnded()
    {
        return $this->hasMany(DowntimeRecord::class, 'ended_by');
    }

    /**
     * Kullanıcının aktivite logları
     */
    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class);
    }

    /**
     * Kullanıcı admin mi?
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Kullanıcı manager mi?
     */
    public function isManager(): bool
    {
        return $this->role === 'manager';
    }

    /**
     * Kullanıcı operator mi?
     */
    public function isOperator(): bool
    {
        return $this->role === 'operator';
    }

    /**
     * Kullanıcı maintenance mi?
     */
    public function isMaintenance(): bool
    {
        return $this->role === 'maintenance';
    }

    /**
     * Son giriş bilgilerini güncelle
     */
    public function updateLastLogin(string $ipAddress): void
    {
        $this->update([
            'last_login_at' => now(),
            'last_login_ip' => $ipAddress,
        ]);
    }
}
