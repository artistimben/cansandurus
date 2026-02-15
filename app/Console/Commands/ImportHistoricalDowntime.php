<?php

namespace App\Console\Commands;

use App\Models\DowntimeRecord;
use App\Models\ErrorCode;
use App\Models\Machine;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
use Illuminate\Support\Facades\DB;

class ImportHistoricalDowntime extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:historical-downtime {file?} {--test : Import only first 50 rows for testing}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import historical downtime records from Excel file';

    protected $stats = [
        'total' => 0,
        'imported' => 0,
        'skipped' => 0,
        'failed' => 0,
    ];

    protected $adminUser;
    protected $errorCodeCache = [];
    protected $machineCache = [];

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $filePath = $this->argument('file') ?? 'DURUŞ RAPORU.xlsx';
        $isTest = $this->option('test');

        // Dosya kontrolü
        if (!file_exists($filePath)) {
            $this->error("❌ File not found: {$filePath}");
            return 1;
        }

        $this->info("📂 Reading Excel file: {$filePath}");

        try {
            $spreadsheet = IOFactory::load($filePath);
            $sheet = $spreadsheet->getActiveSheet();
            $highestRow = $sheet->getHighestRow();

            $this->info("📊 Found {$highestRow} rows");

            if ($isTest) {
                $this->warn("⚠️ TEST MODE: Importing only first 50 rows");
                $highestRow = min(50, $highestRow);
            }

            // Admin kullanıcıyı al
            $this->adminUser = User::where('role', 'admin')->first();
            if (!$this->adminUser) {
                $this->error("❌ Admin user not found!");
                return 1;
            }

            // Cache'leri hazırla
            $this->prepareCache();

            // İmport işlemini başlat
            $this->info("\n🔄 Starting import...\n");

            DB::beginTransaction();

            try {
                $progressBar = $this->output->createProgressBar($highestRow - 4);

                // Satır 5'ten başla (1-3 header, 4 summary)
                for ($row = 5; $row <= $highestRow; $row++) {
                    $this->stats['total']++;

                    try {
                        $result = $this->importRow($sheet, $row);

                        if ($result === 'imported') {
                            $this->stats['imported']++;
                        } elseif ($result === 'skipped') {
                            $this->stats['skipped']++;
                        } else {
                            $this->stats['failed']++;
                        }
                    } catch (\Exception $e) {
                        $this->stats['failed']++;
                        $this->warn("\n❌ Row {$row} failed: " . $e->getMessage());
                    }

                    $progressBar->advance();
                }

                $progressBar->finish();

                if ($isTest) {
                    $this->warn("\n\n⚠️ TEST MODE: Rolling back changes...");
                    DB::rollBack();
                    $this->info("✓ Test completed successfully, no data was saved.");
                } else {
                    DB::commit();
                    $this->info("\n\n✅ Import completed successfully!");
                }

                $this->displayStats();

                return 0;

            } catch (\Exception $e) {
                DB::rollBack();
                $this->error("\n❌ Import failed: " . $e->getMessage());
                return 1;
            }

        } catch (\Exception $e) {
            $this->error("❌ Error reading Excel file: " . $e->getMessage());
            return 1;
        }
    }

    protected function importRow($sheet, $rowNumber)
    {
        // Excel sütunları (header row 3'te belirtilmiş):
        // D: TARİH
        // E: HATA KODU
        // K: OCAK NO (değişti, J'den K'ya)
        // S: SÜRE DK
        // R: DURUŞ NEDENİ

        $dateValue = $sheet->getCell('D' . $rowNumber)->getValue();
        $errorCodeValue = $sheet->getCell('E' . $rowNumber)->getValue();
        $furnaceNoValue = $sheet->getCell('K' . $rowNumber)->getValue();
        $durationValue = $sheet->getCell('S' . $rowNumber)->getValue();
        $descriptionValue = $sheet->getCell('R' . $rowNumber)->getValue();

        // Validasyon: Boş satırları atla
        if (empty($dateValue) || empty($errorCodeValue) || empty($furnaceNoValue)) {
            return 'skipped';
        }

        // Tarihi parse et
        try {
            if (is_numeric($dateValue)) {
                $date = ExcelDate::excelToDateTimeObject($dateValue);
                $startedAt = Carbon::instance($date)->startOfDay();
            } else {
                $startedAt = Carbon::parse($dateValue)->startOfDay();
            }
        } catch (\Exception $e) {
            $this->warn("⚠️ Row {$rowNumber}: Invalid date '{$dateValue}'");
            return 'failed';
        }

        // Süreyi parse et
        $duration = (int) $durationValue;
        if ($duration <= 0) {
            $duration = 30; // Varsayılan 30 dakika
        }

        // Hata kodunu bul veya oluştur
        $errorCode = $this->getOrCreateErrorCode($errorCodeValue, $descriptionValue);
        if (!$errorCode) {
            $this->warn("⚠️ Row {$rowNumber}: Could not find/create error code '{$errorCodeValue}'");
            return 'failed';
        }

        // Makineyi bul
        $machine = $this->getMachine($furnaceNoValue);
        if (!$machine) {
            $this->warn("⚠️ Row {$rowNumber}: Invalid furnace number '{$furnaceNoValue}'");
            return 'failed';
        }

        // Bitiş zamanını hesapla
        $endedAt = $startedAt->copy()->addMinutes($duration);

        // Duplicate kontrolü
        $exists = DowntimeRecord::where('machine_id', $machine->id)
            ->where('started_at', $startedAt)
            ->where('error_code_id', $errorCode->id)
            ->exists();

        if ($exists) {
            return 'skipped';
        }

        // Kaydet
        DowntimeRecord::create([
            'machine_id' => $machine->id,
            'error_code_id' => $errorCode->id,
            'started_at' => $startedAt,
            'ended_at' => $endedAt,
            'duration_minutes' => $duration,
            'description' => $descriptionValue ?? 'Geçmiş kayıt',
            'status' => 'completed',
            'severity' => $errorCode->severity ?? 'medium',
            'started_by' => $this->adminUser->id,
            'ended_by' => $this->adminUser->id,
        ]);

        return 'imported';
    }

    protected function prepareCache()
    {
        $this->info("📦 Preparing cache...");

        // Hata kodlarını cache'e al
        ErrorCode::all()->each(function ($errorCode) {
            $this->errorCodeCache[$errorCode->code] = $errorCode;
        });

        // Makineleri cache'e al
        Machine::all()->each(function ($machine) {
            // OCAK-01 → 1, OCAK-02 → 2
            if (preg_match('/OCAK-(\d+)/', $machine->code, $matches)) {
                $this->machineCache[$matches[1]] = $machine;
            }
        });

        $this->info("✓ Cached " . count($this->errorCodeCache) . " error codes");
        $this->info("✓ Cached " . count($this->machineCache) . " machines");
    }

    protected function getOrCreateErrorCode($code, $description)
    {
        $code = trim($code);

        // Cache'te var mı?
        if (isset($this->errorCodeCache[$code])) {
            return $this->errorCodeCache[$code];
        }

        // Veritabanında var mı?
        $errorCode = ErrorCode::where('code', $code)->first();

        if (!$errorCode) {
            // Yeni hata kodu oluştur (import'tan geldiği için is_active = false)
            $errorCode = ErrorCode::create([
                'code' => $code,
                'name' => substr($description ?? "İmport Kodu {$code}", 0, 100),
                'category' => 'imported',
                'severity' => 'medium',
                'description' => 'Geçmiş verilerden import edildi',
                'is_active' => false, // Sadece raporlarda görünsün
            ]);

            $this->info("\n💡 Created new error code: {$code} (legacy)");
        }

        // Cache'e ekle
        $this->errorCodeCache[$code] = $errorCode;

        return $errorCode;
    }

    protected function getMachine($furnaceNo)
    {
        $furnaceNo = trim($furnaceNo);

        // Cache'te var mı?
        if (isset($this->machineCache[$furnaceNo])) {
            return $this->machineCache[$furnaceNo];
        }

        // OCAK-XX formatında ara
        $machineCode = "OCAK-" . str_pad($furnaceNo, 2, '0', STR_PAD_LEFT);
        $machine = Machine::where('code', $machineCode)->first();

        if ($machine) {
            $this->machineCache[$furnaceNo] = $machine;
        }

        return $machine;
    }

    protected function displayStats()
    {
        $this->info("\n📊 Import Statistics:");
        $this->table(
            ['Status', 'Count'],
            [
                ['Total Rows', $this->stats['total']],
                ['✅ Imported', $this->stats['imported']],
                ['⏭️ Skipped (duplicate)', $this->stats['skipped']],
                ['❌ Failed', $this->stats['failed']],
            ]
        );
    }
}
