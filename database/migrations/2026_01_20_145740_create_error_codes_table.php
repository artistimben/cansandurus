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
     * Hata kodları tablosunu oluşturur
     * Duruş sebeplerini kategorize eder (Mekanik, Elektrik, Kalite vb.)
     */
    public function up(): void
    {
        Schema::create('error_codes', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique()->comment('Hata kodu (örn: E-001)');
            $table->string('category', 100)->comment('Kategori (Mekanik, Elektrik, Kalite, vb.)');
            $table->string('name', 100)->comment('Hata adı');
            $table->text('description')->nullable()->comment('Hata açıklaması');
            $table->boolean('is_active')->default(true)->comment('Hata kodu aktif mi?');
            $table->timestamps();
            $table->softDeletes();

            // Index'ler
            $table->index('is_active');
            $table->index('category');
            $table->index('code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('error_codes');
    }
};
