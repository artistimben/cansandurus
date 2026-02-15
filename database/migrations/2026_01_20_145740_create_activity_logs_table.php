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
     * Aktivite log tablosunu oluşturur
     * Tüm önemli kullanıcı aktivitelerini kaydeder (güvenlik ve audit için)
     */
    public function up(): void
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null')->comment('İşlemi yapan kullanıcı');
            
            $table->string('action', 100)->comment('Yapılan işlem (login, create, update, delete vb.)');
            $table->string('model_type', 100)->nullable()->comment('İşlem yapılan model tipi');
            $table->unsignedBigInteger('model_id')->nullable()->comment('İşlem yapılan kayıt ID');
            
            $table->text('description')->nullable()->comment('İşlem açıklaması');
            $table->json('old_values')->nullable()->comment('Eski değerler (update işlemlerinde)');
            $table->json('new_values')->nullable()->comment('Yeni değerler (update işlemlerinde)');
            
            $table->string('ip_address', 45)->nullable()->comment('Kullanıcı IP adresi');
            $table->string('user_agent', 255)->nullable()->comment('Kullanıcı tarayıcı bilgisi');
            
            $table->timestamp('created_at')->useCurrent()->comment('İşlem zamanı');

            // Index'ler
            $table->index('user_id');
            $table->index('action');
            $table->index('created_at');
            $table->index(['model_type', 'model_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
