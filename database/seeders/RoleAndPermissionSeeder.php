<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

/**
 * Rol ve yetkileri oluşturur
 */
class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Yetkileri oluştur
        $permissions = [
            // Duruş yönetimi
            'downtime.create',
            'downtime.view',
            'downtime.update',
            'downtime.delete',
            'downtime.complete',

            // Makine yönetimi
            'machine.create',
            'machine.view',
            'machine.update',
            'machine.delete',

            // Hata kodu yönetimi
            'error-code.create',
            'error-code.view',
            'error-code.update',
            'error-code.delete',

            // Kullanıcı yönetimi
            'user.create',
            'user.view',
            'user.update',
            'user.delete',

            // Raporlar
            'report.daily',
            'report.monthly',
            'report.yearly',
            'report.export',

            // Activity logs
            'activity-log.view',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Admin rolü - tüm yetkiler
        $adminRole = Role::create(['name' => 'admin']);
        $adminRole->givePermissionTo(Permission::all());

        // Manager rolü - raporlama ve görüntüleme
        $managerRole = Role::create(['name' => 'manager']);
        $managerRole->givePermissionTo([
            'downtime.view',
            'machine.view',
            'error-code.view',
            'report.daily',
            'report.monthly',
            'report.yearly',
            'report.export',
            'activity-log.view',
        ]);

        // Operator rolü - duruş başlat/bitir
        $operatorRole = Role::create(['name' => 'operator']);
        $operatorRole->givePermissionTo([
            'downtime.create',
            'downtime.view',
            'downtime.complete',
            'machine.view',
            'error-code.view',
        ]);

        // Maintenance rolü - bakım ve duruş işlemleri
        $maintenanceRole = Role::create(['name' => 'maintenance']);
        $maintenanceRole->givePermissionTo([
            'downtime.create',
            'downtime.view',
            'downtime.update',
            'downtime.complete',
            'machine.view',
            'error-code.view',
            'report.daily',
        ]);

        $this->command->info('Roller ve yetkiler başarıyla oluşturuldu!');
    }
}
