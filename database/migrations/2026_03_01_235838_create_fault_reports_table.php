<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Duruşa etki etmeyen arıza bildirim tablosu
     */
    public function up(): void
    {
        Schema::create('fault_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('machine_id')->constrained()->onDelete('cascade');
            $table->foreignId('error_code_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('reported_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('resolved_by')->nullable()->constrained('users')->onDelete('set null');

            $table->datetime('reported_at');           // Arıza bildirim zamanı
            $table->datetime('resolved_at')->nullable(); // Çözüm zamanı

            // Arıza detayları
            $table->string('title', 200);              // Kısa başlık
            $table->text('description')->nullable();    // Detaylı açıklama
            $table->text('action_taken')->nullable();   // Alınan önlem / yapılan işlem

            // Öncelik ve durum
            $table->enum('priority', ['low', 'medium', 'high', 'critical'])->default('medium');
            $table->enum('status', ['open', 'in_progress', 'resolved'])->default('open');

            // Üretim devam etti mi?
            $table->boolean('production_continued')->default(true); // Arızaya rağmen üretim devam etti mi

            $table->timestamps();
            $table->softDeletes();

            // İndeksler
            $table->index(['machine_id', 'status']);
            $table->index(['reported_at']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fault_reports');
    }
};
