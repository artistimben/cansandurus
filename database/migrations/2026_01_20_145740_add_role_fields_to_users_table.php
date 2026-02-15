<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    /**
     * Users tablosuna rol ve güvenlik alanları ekler
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['admin', 'manager', 'operator', 'maintenance'])
                  ->default('operator')
                  ->after('email')
                  ->comment('Kullanıcı rolü');
            
            $table->boolean('is_active')->default(true)->after('password')->comment('Kullanıcı aktif mi?');
            $table->timestamp('last_login_at')->nullable()->after('remember_token')->comment('Son giriş zamanı');
            $table->string('last_login_ip', 45)->nullable()->after('last_login_at')->comment('Son giriş IP');
            
            // 2FA alanları
            $table->string('google2fa_secret')->nullable()->after('last_login_ip')->comment('Google 2FA gizli anahtarı');
            $table->boolean('google2fa_enabled')->default(false)->after('google2fa_secret')->comment('2FA aktif mi?');
            
            // Soft delete
            $table->softDeletes();

            // Index'ler
            $table->index('role');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'role',
                'is_active',
                'last_login_at',
                'last_login_ip',
                'google2fa_secret',
                'google2fa_enabled',
                'deleted_at'
            ]);
        });
    }
};
