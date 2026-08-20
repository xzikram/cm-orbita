<?php

namespace App\Console\Commands;

use App\Core\Services\AuditLogService;
use App\Core\Services\SimrsBridgeService;
use App\Models\Clinic;
use App\Models\InpatientFollowUp;
use App\Models\Patient;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SyncInpatientFromSimrs extends Command
{
    protected $signature = 'inpatient:sync-simrs {--limit=100 : Jumlah maksimal data pasien yang ditarik}';
    protected $description = 'Otomatis menarik data pasien pulang rawat inap dari SIM RS untuk jadwal follow-up H+3 (Dijalankan setiap jam 07:00 pagi)';

    public function handle(SimrsBridgeService $simrsBridgeService, AuditLogService $auditLogService): int
    {
        $this->info('Memulai sinkronisasi harian pasien pulang rawat inap dari SIM RS...');
        $limit = (int)$this->option('limit');

        try {
            $simrsPatients = $simrsBridgeService->getDischargedInpatients($limit);
        } catch (\Exception $e) {
            $this->error('Gagal terhubung ke SIM RS: ' . $e->getMessage());
            Log::error('Inpatient SIMRS Sync Error: ' . $e->getMessage());
            return Command::FAILURE;
        }

        if (empty($simrsPatients)) {
            $this->info('Tidak ada data pasien pulang rawat inap dari SIM RS.');
            return Command::SUCCESS;
        }

        $defaultClinic = Clinic::first();
        $clinicId = $defaultClinic ? $defaultClinic->id : 1;

        $createdCount = 0;
        $existingRegs = InpatientFollowUp::where('clinic_id', $clinicId)
            ->whereNotNull('registration_no')
            ->pluck('registration_no')
            ->toArray();

        foreach ($simrsPatients as $row) {
            $regNo = $row['registration_no'];
            if (!$regNo || in_array($regNo, $existingRegs)) {
                continue;
            }

            $dischargeDate = $row['discharge_date'] ? Carbon::parse($row['discharge_date']) : null;
            if (!$dischargeDate) {
                continue;
            }

            // Hitung tanggal follow-up jatuh tempo H+3 (Discharge Date + 3 hari)
            $dueDate = $dischargeDate->copy()->addDays(3)->toDateString();

            // Link ke pasien lokal jika ada
            $patient = Patient::where('clinic_id', $clinicId)
                ->where('medical_record_number', $row['medical_record_number'])
                ->first();

            InpatientFollowUp::create([
                'clinic_id' => $clinicId,
                'patient_id' => $patient?->id,
                'registration_no' => $regNo,
                'medical_record_number' => $row['medical_record_number'],
                'patient_name' => $row['patient_name'],
                'patient_phone' => $row['patient_phone'],
                'patient_age' => $row['patient_age'],
                'gender' => $row['gender'],
                'admission_date' => $row['admission_date'] ? Carbon::parse($row['admission_date']) : null,
                'discharge_date' => $dischargeDate,
                'follow_up_due_date' => $dueDate,
                'room_bed' => $row['room_bed'],
                'doctor_dpjp' => $row['doctor_dpjp'],
                'diagnosis_or_procedure' => $row['discharge_notes'] ?: ($row['initial_diagnose'] ?: null),
                'status' => 'pending',
                'source' => 'simrs',
            ]);

            $existingRegs[] = $regNo;
            $createdCount++;
        }

        $this->info("Sinkronisasi selesai: {$createdCount} data pasien rawat inap baru berhasil dimasukkan ke antrean follow-up H+3.");
        Log::info("Inpatient SIMRS Sync Completed: {$createdCount} new records synced at 07:00 AM.");

        return Command::SUCCESS;
    }
}
