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
     * Duruş kayıtları tablosunu oluşturur
     * Her duruş kaydı için makine, hata kodu, başlangıç/bitiş zamanı ve notlar saklanır
     */
    public function up(): void
    {
        Schema::create('downtime_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('machine_id')->constrained()->onDelete('cascade')->comment('İlgili makine');
            $table->foreignId('error_code_id')->constrained()->onDelete('restrict')->comment('Hata kodu');
            $table->foreignId('started_by')->constrained('users')->onDelete('restrict')->comment('Duruşu başlatan kullanıcı');
            $table->foreignId('ended_by')->nullable()->constrained('users')->onDelete('restrict')->comment('Duruşu bitiren kullanıcı');
            
            $table->timestamp('started_at')->comment('Duruş başlangıç zamanı');
            $table->timestamp('ended_at')->nullable()->comment('Duruş bitiş zamanı');
            $table->integer('duration_minutes')->nullable()->comment('Duruş süresi (dakika)');
            
            $table->text('notes')->nullable()->comment('Duruş notları');
            $table->enum('status', ['active', 'completed', 'cancelled'])->default('active')->comment('Duruş durumu');
            
            $table->timestamps();
            $table->softDeletes();

            // Index'ler - Raporlama için önemli
            $table->index('started_at');
            $table->index('ended_at');
            $table->index('status');
            $table->index(['machine_id', 'started_at']);
            $table->index(['error_code_id', 'started_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('downtime_records');
    }
};
