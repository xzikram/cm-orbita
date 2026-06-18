<?php

namespace Database\Seeders;

use App\Models\Clinic;
use App\Models\Doctor;
use App\Models\Examination;
use App\Models\FollowUpSchedule;
use App\Models\Patient;
use App\Models\RefractionOptician;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class SampleDataSeeder extends Seeder
{
    public function run(): void
    {
        $clinic = Clinic::first();
        $doctor = Doctor::first();
        $ro = RefractionOptician::first();
        $creator = User::whereHas('roles', fn($q) => $q->where('name', 'admin-klinik'))->first();

        // Create sample patients
        $patients = [
            ['medical_record_number' => 'RM-2026-001', 'name' => 'Andi Wijaya', 'phone' => '081311111001', 'gender' => 'L', 'date_of_birth' => '1990-05-15', 'address' => 'Jl. Merdeka No. 10, Jakarta'],
            ['medical_record_number' => 'RM-2026-002', 'name' => 'Sari Indah', 'phone' => '081311111002', 'gender' => 'P', 'date_of_birth' => '1985-08-22', 'address' => 'Jl. Sudirman No. 25, Jakarta'],
            ['medical_record_number' => 'RM-2026-003', 'name' => 'Rizki Pratama', 'phone' => '081311111003', 'gender' => 'L', 'date_of_birth' => '1995-01-10', 'address' => 'Jl. Gatot Subroto No. 5, Jakarta'],
            ['medical_record_number' => 'RM-2026-004', 'name' => 'Maya Sari', 'phone' => '081311111004', 'gender' => 'P', 'date_of_birth' => '1988-12-03', 'address' => 'Jl. Thamrin No. 18, Jakarta'],
            ['medical_record_number' => 'RM-2026-005', 'name' => 'Budi Hartono', 'phone' => '081311111005', 'gender' => 'L', 'date_of_birth' => '1992-07-20', 'address' => 'Jl. Kuningan No. 7, Jakarta'],
            ['medical_record_number' => 'RM-2026-006', 'name' => 'Ratna Dewi', 'phone' => '081311111006', 'gender' => 'P', 'date_of_birth' => '1998-03-14', 'address' => 'Jl. Casablanca No. 33, Jakarta'],
            ['medical_record_number' => 'RM-2026-007', 'name' => 'Dimas Prasetyo', 'phone' => '081311111007', 'gender' => 'L', 'date_of_birth' => '1993-11-28', 'address' => 'Jl. HR Rasuna Said No. 12, Jakarta'],
            ['medical_record_number' => 'RM-2026-008', 'name' => 'Fitri Handayani', 'phone' => '081311111008', 'gender' => 'P', 'date_of_birth' => '2000-06-09', 'address' => 'Jl. Jend. Sudirman No. 45, Jakarta'],
        ];

        $intervals = config('cfms.follow_up_intervals');

        foreach ($patients as $index => $patientData) {
            $patient = Patient::create(array_merge($patientData, [
                'clinic_id' => $clinic->id,
                'is_active' => true,
            ]));

            // Create examination with different dates
            $examDate = Carbon::now()->subDays(rand(1, 60));

            $examination = Examination::create([
                'clinic_id' => $clinic->id,
                'patient_id' => $patient->id,
                'doctor_id' => $doctor->id,
                'ro_id' => $ro->id,
                'created_by' => $creator->id,
                'examination_date' => $examDate,
                'od_sphere' => round(rand(-800, 200) / 100, 2),
                'od_cylinder' => round(rand(-300, 0) / 100, 2),
                'od_axis' => rand(0, 180),
                'od_visus' => $this->randomVisus(),
                'os_sphere' => round(rand(-800, 200) / 100, 2),
                'os_cylinder' => round(rand(-300, 0) / 100, 2),
                'os_axis' => rand(0, 180),
                'os_visus' => $this->randomVisus(),
                'lens_type' => collect(['Soft Lens', 'RGP', 'Toric', 'Multifocal'])->random(),
                'lens_brand' => collect(['Acuvue', 'Bausch+Lomb', 'CooperVision', 'Alcon'])->random(),
                'clinical_notes' => 'Pemeriksaan awal lensa kontak. Kondisi mata baik.',
                'status' => 'active',
            ]);

            // Auto-generate follow-up schedules
            foreach ($intervals as $seq => $interval) {
                $scheduledDate = $examDate->copy()->addDays($interval['days']);
                $status = $scheduledDate->isPast() ? (rand(0, 3) > 0 ? 'completed' : 'missed') : 'pending';

                FollowUpSchedule::create([
                    'examination_id' => $examination->id,
                    'patient_id' => $patient->id,
                    'clinic_id' => $clinic->id,
                    'label' => $interval['label'],
                    'interval_days' => $interval['days'],
                    'scheduled_date' => $scheduledDate,
                    'sequence' => $seq + 1,
                    'status' => $status,
                ]);
            }
        }
    }

    private function randomVisus(): string
    {
        $values = ['6/6', '6/7.5', '6/9', '6/12', '6/15', '6/18', '6/24', '6/36'];
        return $values[array_rand($values)];
    }
}
