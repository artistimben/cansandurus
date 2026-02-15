<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Machine;
use App\Models\ErrorCode;
use App\Models\DowntimeRecord;
use App\Models\User;

class ImportExcelDowntime extends Command
{
    protected $signature = 'import:excel-downtime {file}';
    protected $description = 'Excel dosyasından geçmiş duruş kayıtlarını içe aktar';

    private $machines;
    private $errorCodes;
    private $defaultUser;
    private $imported = 0;
    private $skipped = 0;
    private $errors = [];

    public function handle()
    {
        $file = $this->argument('file');

        if (!file_exists($file)) {
            $this->error("Dosya bulunamadı: {$file}");
            return 1;
        }

        $this->info("📂 Excel dosyası okunuyor: {$file}");

        // Cache makineler ve hata kodları
        $this->machines = Machine::all()->keyBy('code');
        $this->errorCodes = ErrorCode::all()->keyBy('code');
        $this->defaultUser = User::where('email', 'admin@cansan.local')->first();

        if (!$this->defaultUser) {
            $this->error('Admin kullanıcısı bulunamadı!');
            return 1;
        }

        // Unzip Excel
        $tempDir = sys_get_temp_dir() . '/excel_import_' . uniqid();
        mkdir($tempDir);

        $this->info("📦 Excel dosyası açılıyor...");
        exec("unzip -q '{$file}' -d '{$tempDir}'");

        // SharedStrings oku
        $sharedStringsFile = $tempDir . '/xl/sharedStrings.xml';
        $sharedStrings = $this->parseSharedStrings($sharedStringsFile);

        $this->info("📊 Shared strings: " . count($sharedStrings) . " adet");

        // Her sheet'i işle
        $sheets = glob($tempDir . '/xl/worksheets/sheet*.xml');
        $this->info("📄 " . count($sheets) . " sheet bulundu");

        foreach ($sheets as $sheetFile) {
            $sheetName = basename($sheetFile, '.xml');
            $this->info("\n🔄 İşleniyor: {$sheetName}");

            $this->processSheet($sheetFile, $sharedStrings);
        }

        // Cleanup
        exec("rm -rf '{$tempDir}'");

        // Sonuçlar
        $this->newLine();
        $this->info("✅ İçe aktarma tamamlandı!");
        $this->table(
            ['Durum', 'Sayı'],
            [
                ['Başarılı', $this->imported],
                ['Atlanan', $this->skipped],
                ['Hata', count($this->errors)],
            ]
        );

        if ($this->errors) {
            $this->warn("\n⚠️  Hatalar:");
            foreach (array_slice($this->errors, 0, 10) as $error) {
                $this->line("  - {$error}");
            }
            if (count($this->errors) > 10) {
                $this->line("  ... ve " . (count($this->errors) - 10) . " hata daha");
            }
        }

        return 0;
    }

    private function parseSharedStrings($file)
    {
        if (!file_exists($file)) {
            return [];
        }

        $xml = file_get_contents($file);
        preg_match_all('/<t[^>]*>([^<]*)<\/t>/', $xml, $matches);

        return $matches[1] ?? [];
    }

    private function processSheet($sheetFile, $sharedStrings)
    {
        $xml = simplexml_load_file($sheetFile);

        if (!$xml || !isset($xml->sheetData)) {
            $this->warn("  Sheet verisi bulunamadı");
            return;
        }

        $rowCount = 0;

        foreach ($xml->sheetData->row as $row) {
            $rowData = $this->parseRow($row, $sharedStrings);

            if ($this->isValidDowntimeRow($rowData)) {
                $this->importDowntimeRecord($rowData);
                $rowCount++;
            }
        }

        $this->line("  → {$rowCount} satır işlendi");
    }

    private function parseRow($row, $sharedStrings)
    {
        $data = [];

        foreach ($row->c as $cell) {
            $ref = (string) $cell['r'];
            $column = preg_replace('/[0-9]+/', '', $ref);

            $value = '';
            if (isset($cell->v)) {
                $value = (string) $cell->v;

                // Eğer shared string referansı ise
                if (isset($cell['t']) && (string) $cell['t'] === 's') {
                    $index = (int) $value;
                    $value = $sharedStrings[$index] ?? '';
                }
            }

            $data[$column] = $value;
        }

        return $data;
    }

    private function isValidDowntimeRow($data)
    {
        // Tarih (E kolonu) ve süre (F kolonu) olmalı
        return isset($data['E']) && isset($data['F']) &&
            is_numeric($data['E']) && is_numeric($data['F']) &&
            $data['F'] > 0; // Süre > 0
    }

    private function importDowntimeRecord($data)
    {
        try {
            // Excel date'i Carbon tarihine çevir
            // Excel stores dates as days since 1900-01-01 (with a bug for leap year 1900)
            $excelDate = (float) $data['E'];

            // Convert Excel serial date to Carbon
            // Excel epoch: 1899-12-30 (0 = 1900-01-01 withminus 1)
            // For dates after 1900-03-01, subtract 1 for the leap year bug
            $unixTimestamp = ($excelDate - 25569) * 86400; // 25569 = days between 1900-01-01 and 1970-01-01
            $startDate = \Carbon\Carbon::createFromTimestamp($unixTimestamp);

            $durationMinutes = (int) $data['F'];
            $endDate = $startDate->copy()->addMinutes($durationMinutes);

            // Açıklama (G-J merged)
            $description = $data['G'] ?? '';

            // Hata kodu eşleştir
            $errorCode = $this->matchErrorCode($description);

            // Random makine seç (gerçek veriye göre ayarlanabilir)
            $machine = $this->machines->random();

            // Duruş kaydı oluştur
            DowntimeRecord::create([
                'machine_id' => $machine->id,
                'error_code_id' => $errorCode->id,
                'started_by' => $this->defaultUser->id,
                'ended_by' => $this->defaultUser->id,
                'started_at' => $startDate,
                'ended_at' => $endDate,
                'duration_minutes' => $durationMinutes,
                'notes' => mb_substr($description, 0, 1000),
                'status' => 'completed',
            ]);

            $this->imported++;

        } catch (\Exception $e) {
            $this->errors[] = "Satır hatası: " . $e->getMessage();
            $this->skipped++;
        }
    }

    private function matchErrorCode($description)
    {
        $desc = mb_strtolower($description);

        // Anahtar kelime eşleştirme
        if (str_contains($desc, 'çatt') || str_contains($desc, 'catt')) {
            return $this->errorCodes->get('E-001'); // Ocak çatlaması
        }
        if (str_contains($desc, 'ark')) {
            return $this->errorCodes->get('E-002'); // Ark yapması
        }
        if (str_contains($desc, 'enerji') || str_contains($desc, 'kesti')) {
            return $this->errorCodes->get('E-003'); // Enerji kesintisi
        }
        if (str_contains($desc, 'su kaça')) {
            return $this->errorCodes->get('E-004'); // Su kaçağı
        }
        if (str_contains($desc, 'düşük')) {
            return $this->errorCodes->get('E-005'); // Düşük güç
        }
        if (str_contains($desc, 'hurda zayıf') || str_contains($desc, 'zayif')) {
            return $this->errorCodes->get('E-101'); // Hurda zayıf
        }
        if (str_contains($desc, 'toprak')) {
            return $this->errorCodes->get('E-102'); // Hurda topraklı
        }
        if (str_contains($desc, 'manyetik') || str_contains($desc, 'yetiş')) {
            return $this->errorCodes->get('E-103'); // Manyetik hurda
        }
        if (str_contains($desc, 'vinç') || str_contains($desc, 'vinci')) {
            return $this->errorCodes->get('E-201'); // Vinç arızası
        }
        if (str_contains($desc, 'tek araba')) {
            return $this->errorCodes->get('E-203'); // Tek araba
        }
        if (str_contains($desc, 'bakım')) {
            return $this->errorCodes->get('M-001'); // Bakım
        }
        if (str_contains($desc, 'temiz')) {
            return $this->errorCodes->get('M-002'); // Temizlik
        }
        if (str_contains($desc, 'cüruf') || str_contains($desc, 'curuf')) {
            return $this->errorCodes->get('M-003'); // Cüruf alma
        }
        if (str_contains($desc, 'astar') || str_contains($desc, 'ölçü')) {
            return $this->errorCodes->get('M-004'); // Astar/ölçüm
        }
        if (str_contains($desc, 'ccm') || str_contains($desc, 'kalıp')) {
            return $this->errorCodes->get('M-005'); // CCM kalıp
        }
        if (str_contains($desc, 'karbon')) {
            return $this->errorCodes->get('E-301'); // Karbon yüksek
        }
        if (str_contains($desc, 'duman')) {
            return $this->errorCodes->get('E-401'); // Duman
        }
        if (str_contains($desc, 'ilave') || str_contains($desc, 'İlave')) {
            return $this->errorCodes->get('O-001'); // İlave
        }
        if (str_contains($desc, 'paçal')) {
            return $this->errorCodes->get('O-002'); // Paçal
        }

        // Default: Ocak bakımı
        return $this->errorCodes->get('M-001');
    }
}
