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
     * Makine/Hat tablosunu oluşturur
     * Her makine için kod, isim, lokasyon ve aktiflik durumu saklanır
     */
    public function up(): void
    {
        Schema::create('machines', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique()->comment('Makine kodu (örn: HH-01)');
            $table->string('name', 100)->comment('Makine adı');
            $table->string('location', 100)->nullable()->comment('Makine lokasyonu');
            $table->text('description')->nullable()->comment('Makine açıklaması');
            $table->boolean('is_active')->default(true)->comment('Makine aktif mi?');
            $table->timestamps();
            $table->softDeletes();

            // Index'ler
            $table->index('is_active');
            $table->index('code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('machines');
    }
};
