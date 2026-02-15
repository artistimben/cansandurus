<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * Ana Seeder - Tüm seeder'ları çalıştırır
 */
class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleAndPermissionSeeder::class,
            AdminUserSeeder::class,
            SampleDataSeeder::class,
        ]);

        $this->command->info('Tüm seeder\'lar başarıyla çalıştırıldı!');
    }
}
