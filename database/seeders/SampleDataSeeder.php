<?php

namespace Database\Seeders;

use App\Models\Machine;
use App\Models\ErrorCode;
use Illuminate\Database\Seeder;

/**
 * Örnek makine ve hata kodları oluşturur
 */
class SampleDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Örnek Makineler
        $machines = [
            ['code' => 'HH-01', 'name' => 'Hadde Hattı 1', 'location' => 'A Blok', 'description' => 'Ana hadde hattı'],
            ['code' => 'HH-02', 'name' => 'Hadde Hattı 2', 'location' => 'A Blok', 'description' => 'Yedek hadde hattı'],
            ['code' => 'KM-01', 'name' => 'Kesme Makinesi 1', 'location' => 'B Blok', 'description' => 'Otomatik kesme makinesi'],
            ['code' => 'KB-01', 'name' => 'Kaynak Birimi 1', 'location' => 'C Blok', 'description' => 'Kaynak istasyonu'],
            ['code' => 'PH-01', 'name' => 'Paketleme Hattı', 'location' => 'D Blok', 'description' => 'Ürün paketleme'],
        ];

        foreach ($machines as $machine) {
            Machine::create([
                'code' => $machine['code'],
                'name' => $machine['name'],
                'location' => $machine['location'],
                'description' => $machine['description'],
                'is_active' => true,
            ]);
        }

        $this->command->info('Örnek makineler oluşturuldu!');

        // Örnek Hata Kodları
        $errorCodes = [
            // Mekanik Hatalar
            ['code' => 'M-001', 'category' => 'Mekanik', 'name' => 'Rulman Arızası', 'description' => 'Rulman değişimi gerekiyor'],
            ['code' => 'M-002', 'category' => 'Mekanik', 'name' => 'Kayış Kopması', 'description' => 'Kayış değişimi gerekiyor'],
            ['code' => 'M-003', 'category' => 'Mekanik', 'name' => 'Vites Kutusu Arızası', 'description' => 'Vites kutusu tamiri'],
            ['code' => 'M-004', 'category' => 'Mekanik', 'name' => 'Hidrolik Sızıntı', 'description' => 'Hidrolik sistem kontrolü'],

            // Elektrik Hatalar
            ['code' => 'E-001', 'category' => 'Elektrik', 'name' => 'Motor Arızası', 'description' => 'Motor tamiri/değişimi'],
            ['code' => 'E-002', 'category' => 'Elektrik', 'name' => 'Kablo Arızası', 'description' => 'Kablo kontrolü ve tamiri'],
            ['code' => 'E-003', 'category' => 'Elektrik', 'name' => 'Sensör Arızası', 'description' => 'Sensör değişimi'],
            ['code' => 'E-004', 'category' => 'Elektrik', 'name' => 'PLC Hatası', 'description' => 'PLC programı kontrolü'],

            // Kalite Hataları
            ['code' => 'K-001', 'category' => 'Kalite', 'name' => 'Ölçü Hata', 'description' => 'Ölçü dışı üretim'],
            ['code' => 'K-002', 'category' => 'Kalite', 'name' => 'Yüzey Hatası', 'description' => 'Yüzey kalitesi düşük'],
            ['code' => 'K-003', 'category' => 'Kalite', 'name' => 'Malzeme Hatası', 'description' => 'Hammadde kalite problemi'],

            // Operasyonel Hatalar
            ['code' => 'O-001', 'category' => 'Operasyonel', 'name' => 'Malzeme Bekleme', 'description' => 'Hammadde bekliyor'],
            ['code' => 'O-002', 'category' => 'Operasyonel', 'name' => 'Operatör Yok', 'description' => 'Operatör bulunamadı'],
            ['code' => 'O-003', 'category' => 'Operasyonel', 'name' => 'Temizlik', 'description' => 'Planlı temizlik'],
            ['code' => 'O-004', 'category' => 'Operasyonel', 'name' => 'Ayar', 'description' => 'Makine ayarı yapılıyor'],

            // Planlı Duruşlar
            ['code' => 'P-001', 'category' => 'Planlı', 'name' => 'Bakım', 'description' => 'Planlı bakım çalışması'],
            ['code' => 'P-002', 'category' => 'Planlı', 'name' => 'Mola', 'description' => 'Çalışan molası'],
            ['code' => 'P-003', 'category' => 'Planlı', 'name' => 'Vardiya Değişimi', 'description' => 'Vardiya arası'],
        ];

        foreach ($errorCodes as $errorCode) {
            ErrorCode::create([
                'code' => $errorCode['code'],
                'category' => $errorCode['category'],
                'name' => $errorCode['name'],
                'description' => $errorCode['description'],
                'is_active' => true,
            ]);
        }

        $this->command->info('Örnek hata kodları oluşturuldu!');
    }
}
