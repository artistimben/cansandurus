<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ErrorCode;

class ErrorCodeSeeder extends Seeder
{
    /**
     * Hata Kodları - DURUŞ RAPORU.xlsx analizinden çıkarıldı
     */
    public function run(): void
    {
        $errorCodes = [
            // OCAK ARIZALARI (E-0xx)
            [
                'code' => 'E-001',
                'name' => 'Ocak Çatlaması',
                'description' => 'Ocak çatladı, arıza oluştu',
                'category' => 'Ocak Arızası',
                'severity' => 'high',
                'is_active' => true,
            ],
            [
                'code' => 'E-002',
                'name' => 'Ocak Ark Yapması',
                'description' => 'Ocakta elektrik arkı oluştu',
                'category' => 'Ocak Arızası',
                'severity' => 'high',
                'is_active' => true,
            ],
            [
                'code' => 'E-003',
                'name' => 'Ocak Enerji Kesintisi',
                'description' => 'Ocak enerjisi kesildi',
                'category' => 'Ocak Arızası',
                'severity' => 'high',
                'is_active' => true,
            ],
            [
                'code' => 'E-004',
                'name' => 'Ocak Su Kaçağı',
                'description' => 'Ocakta su kaçağı tespit edildi',
                'category' => 'Ocak Arızası',
                'severity' => 'high',
                'is_active' => true,
            ],
            [
                'code' => 'E-005',
                'name' => 'Ocak Düşük Güç',
                'description' => 'Ocak düşük güçte çalışıyor',
                'category' => 'Ocak Arızası',
                'severity' => 'medium',
                'is_active' => true,
            ],

            // HURDA SORUNLARI (E-1xx)
            [
                'code' => 'E-101',
                'name' => 'Hurda Zayıf/Kalitesiz',
                'description' => 'Hurda kalitesi düşük, zayıf',
                'category' => 'Malzeme Sorunu',
                'severity' => 'medium',
                'is_active' => true,
            ],
            [
                'code' => 'E-102',
                'name' => 'Hurda Topraklı',
                'description' => 'Hurda topraklı, elektrik iletimi sorunu',
                'category' => 'Malzeme Sorunu',
                'severity' => 'medium',
                'is_active' => true,
            ],
            [
                'code' => 'E-103',
                'name' => 'Manyetik Hurda Yetersizliği',
                'description' => 'Manyetik hurda yetiştiremedi, tedarik sorunu',
                'category' => 'Malzeme Sorunu',
                'severity' => 'medium',
                'is_active' => true,
            ],

            // VİNÇ ARIZALARI (E-2xx)
            [
                'code' => 'E-201',
                'name' => 'Şarj Vinci Arızası',
                'description' => 'Şarj vinci arızalandı',
                'category' => 'Mekanik Arıza',
                'severity' => 'high',
                'is_active' => true,
            ],
            [
                'code' => 'E-202',
                'name' => 'Şarj Vinci Yetersizliği',
                'description' => 'Şarj vinci yetiştiremedi, hurda alamıyor',
                'category' => 'Mekanik Arıza',
                'severity' => 'medium',
                'is_active' => true,
            ],
            [
                'code' => 'E-203',
                'name' => 'Tek Araba Çalışma',
                'description' => 'Tek hurda arabası ile çalışıldı',
                'category' => 'Mekanik Arıza',
                'severity' => 'low',
                'is_active' => true,
            ],

            // BAKIM İŞLEMLERİ (M-0xx)
            [
                'code' => 'M-001',
                'name' => 'Ocak Bakımı',
                'description' => 'Planlı ocak bakımı yapıldı',
                'category' => 'Planlı Bakım',
                'severity' => 'low',
                'is_active' => true,
            ],
            [
                'code' => 'M-002',
                'name' => 'Ocak Temizliği',
                'description' => 'Ocak ağzı temizlendi',
                'category' => 'Planlı Bakım',
                'severity' => 'low',
                'is_active' => true,
            ],
            [
                'code' => 'M-003',
                'name' => 'Cüruf Alma',
                'description' => 'Cüruf alındı (rutin operasyon)',
                'category' => 'Planlı Bakım',
                'severity' => 'low',
                'is_active' => true,
            ],
            [
                'code' => 'M-004',
                'name' => 'Astar Ölçümü',
                'description' => 'Ocak astar ölçümü yapıldı',
                'category' => 'Planlı Bakım',
                'severity' => 'low',
                'is_active' => true,
            ],
            [
                'code' => 'M-005',
                'name' => 'CCM Kalıp Değişimi',
                'description' => 'CCM kalıp değişimi beklendi',
                'category' => 'Planlı Bakım',
                'severity' => 'medium',
                'is_active' => true,
            ],

            // KİMYASAL SORUNLAR (E-3xx)
            [
                'code' => 'E-301',
                'name' => 'Karbon Yüksek',
                'description' => 'Karbon seviyesi yüksek geldi, müdahale edildi',
                'category' => 'Kimyasal Sorun',
                'severity' => 'medium',
                'is_active' => true,
            ],

            // ÇEVRESEL SORUNLAR (E-4xx)
            [
                'code' => 'E-401',
                'name' => 'Yoğun Duman',
                'description' => 'Yoğun dumandan dolayı ocak kapatıldı',
                'category' => 'Çevresel Sorun',
                'severity' => 'medium',
                'is_active' => true,
            ],

            // İLAVE İŞLEMLER (O-0xx)
            [
                'code' => 'O-001',
                'name' => 'İlave Verme',
                'description' => 'Ocağa ilave malzeme verildi',
                'category' => 'Operasyon',
                'severity' => 'low',
                'is_active' => true,
            ],
            [
                'code' => 'O-002',
                'name' => 'Paçal İşlemi',
                'description' => 'Paçal yapıldı',
                'category' => 'Operasyon',
                'severity' => 'low',
                'is_active' => true,
            ],
            [
                'code' => 'O-003',
                'name' => 'Ölçü Atma',
                'description' => 'Ocağa ölçü atıldı',
                'category' => 'Operasyon',
                'severity' => 'low',
                'is_active' => true,
            ],
        ];

        foreach ($errorCodes as $errorCode) {
            ErrorCode::updateOrCreate(
                ['code' => $errorCode['code']],
                $errorCode
            );
        }

        $this->command->info('✅ ' . count($errorCodes) . ' hata kodu başarıyla oluşturuldu.');
    }
}
