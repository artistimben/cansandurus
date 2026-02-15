<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Machine;

class MachineSeeder extends Seeder
{
    /**
     * CANSAN Çelik Fabrikası - 6 Ocak Makinesi
     * 
     * Yapı:
     * - 3 Set
     * - Her sette 2 Ocak
     * - Toplam: 6 Ocak (Ocak 1 - Ocak 6)
     */
    public function run(): void
    {
        $machines = [
            // SET 1
            [
                'code' => 'OCAK-01',
                'name' => '1. Ocak',
                'location' => 'Set 1',
                'description' => 'Elektrik Ark Ocağı - Set 1, 1. Ocak',
                'is_active' => true,
            ],
            [
                'code' => 'OCAK-02',
                'name' => '2. Ocak',
                'location' => 'Set 1',
                'description' => 'Elektrik Ark Ocağı - Set 1, 2. Ocak',
                'is_active' => true,
            ],

            // SET 2
            [
                'code' => 'OCAK-03',
                'name' => '3. Ocak',
                'location' => 'Set 2',
                'description' => 'Elektrik Ark Ocağı - Set 2, 1. Ocak',
                'is_active' => true,
            ],
            [
                'code' => 'OCAK-04',
                'name' => '4. Ocak',
                'location' => 'Set 2',
                'description' => 'Elektrik Ark Ocağı - Set 2, 2. Ocak',
                'is_active' => true,
            ],

            // SET 3
            [
                'code' => 'OCAK-05',
                'name' => '5. Ocak',
                'location' => 'Set 3',
                'description' => 'Elektrik Ark Ocağı - Set 3, 1. Ocak',
                'is_active' => true,
            ],
            [
                'code' => 'OCAK-06',
                'name' => '6. Ocak',
                'location' => 'Set 3',
                'description' => 'Elektrik Ark Ocağı - Set 3, 2. Ocak',
                'is_active' => true,
            ],
        ];

        foreach ($machines as $machine) {
            Machine::updateOrCreate(
                ['code' => $machine['code']], // Unique identifier
                $machine
            );
        }

        $this->command->info('✅ 6 Ocak makinesi başarıyla oluşturuldu.');
    }
}
