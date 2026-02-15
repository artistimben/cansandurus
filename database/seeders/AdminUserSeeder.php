<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * İlk admin kullanıcısını oluşturur
 */
class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin kullanıcısı oluştur
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@cansan.local',
            'password' => Hash::make('Admin@Cansan2026'), // Güçlü şifre
            'role' => 'admin',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        // Admin rolünü ata
        $admin->assignRole('admin');

        $this->command->info('Admin kullanıcısı oluşturuldu!');
        $this->command->info('Email: admin@cansan.local');
        $this->command->info('Şifre: Admin@Cansan2026');
        $this->command->warn('ÖNEMLÄ°: Lütfen bu şifreyi hemen değiştirin!');

        // Örnek kullanıcılar
        $manager = User::create([
            'name' => 'Mehmet Yılmaz',
            'email' => 'manager@cansan.local',
            'password' => Hash::make('Manager@Cansan2026'),
            'role' => 'manager',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
        $manager->assignRole('manager');

        $operator = User::create([
            'name' => 'Ahmet Demir',
            'email' => 'operator@cansan.local',
            'password' => Hash::make('Operator@Cansan2026'),
            'role' => 'operator',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
        $operator->assignRole('operator');

        $maintenance = User::create([
            'name' => 'Ali Kaya',
            'email' => 'maintenance@cansan.local',
            'password' => Hash::make('Maintenance@Cansan2026'),
            'role' => 'maintenance',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
        $maintenance->assignRole('maintenance');

        $this->command->info('Örnek kullanıcılar oluşturuldu!');
    }
}
