<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Null olan category değerlerini 'other' yap
        DB::table('error_codes')
            ->whereNull('category')
            ->update(['category' => 'other']);

        // Category kolonunu NOT NULL yap
        Schema::table('error_codes', function (Blueprint $table) {
            $table->string('category')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('error_codes', function (Blueprint $table) {
            $table->string('category')->nullable()->change();
        });
    }
};
