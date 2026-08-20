<?php

namespace App\Core\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SimrsBridgeService
{
    protected string $url;
    protected string $token;

    public function __construct()
    {
        $config = config('cfms.simrs');
        $this->url = $config['bridge_url'] ?? 'http://192.168.40.141:88/qc/bridge.ashx';
        $this->token = $config['bridge_token'] ?? 'OrbitaSecureBridge2026';
    }

    /**
     * Execute a SQL query on SIM RS bridge API.
     */
    public function query(string $sql): array
    {
        try {
            $endpoint = $this->url . '?token=' . urlencode($this->token);
            $response = Http::asForm()
                ->timeout(10)
                ->post($endpoint, [
                    'query' => $sql,
                ]);

            if ($response->successful()) {
                $data = $response->json();
                return is_array($data) ? $data : [];
            }

            Log::error('SIM RS bridge query failed: ' . $response->body());
            return [];
        } catch (\Exception $e) {
            Log::error('Exception in SIM RS bridge query: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get list of patients registered today in SIM RS.
     */
    public function getTodayPatients(int $limit = 50): array
    {
        $sql = "
            SELECT TOP {$limit} 
                r.RegistrationNo, 
                r.PatientID, 
                p.MedicalNo,
                p.FirstName, 
                p.LastName, 
                p.DateOfBirth, 
                p.PhoneNo, 
                p.MobilePhoneNo, 
                p.Email,
                r.RegistrationDate
            FROM Registration r
            LEFT JOIN Patient p ON r.PatientID = p.PatientID
            WHERE r.RegistrationDate >= CONVERT(date, GETDATE())
              AND r.IsVoid = 0
            ORDER BY r.RegistrationDate DESC, r.CreatedDateTime DESC
        ";

        return $this->formatPatients($this->query($sql));
    }

    /**
     * Search patients in SIM RS by term (Name or PatientID/MedicalNo/Phone).
     */
    public function searchPatients(string $term, int $limit = 20): array
    {
        $cleanTerm = str_replace("'", "''", trim($term));

        if (empty($cleanTerm)) {
            return $this->getTodayPatients($limit);
        }

        $sql = "
            SELECT TOP {$limit} 
                p.PatientID, 
                p.MedicalNo,
                p.FirstName, 
                p.LastName, 
                p.DateOfBirth, 
                p.PhoneNo, 
                p.MobilePhoneNo, 
                p.Email
            FROM Patient p
            WHERE p.MedicalNo LIKE '%{$cleanTerm}%'
               OR p.PatientID LIKE '%{$cleanTerm}%'
               OR p.FirstName LIKE '%{$cleanTerm}%'
               OR p.LastName LIKE '%{$cleanTerm}%'
               OR p.PhoneNo LIKE '%{$cleanTerm}%'
               OR p.MobilePhoneNo LIKE '%{$cleanTerm}%'
            ORDER BY p.LastUpdateDateTime DESC
        ";

        return $this->formatPatients($this->query($sql));
    }

    /**
     * Helper to format raw SIM RS patient objects into standard array.
     */
    protected function formatPatients(array $rows): array
    {
        $results = [];
        foreach ($rows as $row) {
            $firstName = trim($row['FirstName'] ?? '');
            $lastName = trim($row['LastName'] ?? '');
            $name = trim($firstName . ' ' . ($lastName !== '.' && $lastName !== '-' ? $lastName : ''));
            
            $phone = !empty($row['MobilePhoneNo']) ? $row['MobilePhoneNo'] : ($row['PhoneNo'] ?? '');
            $phone = preg_replace('/\D/', '', $phone);
            if (str_starts_with($phone, '62')) {
                $phone = '0' . substr($phone, 2);
            }

            $dob = null;
            if (!empty($row['DateOfBirth'])) {
                $dobParts = explode(' ', $row['DateOfBirth']);
                $dob = $dobParts[0] ?? null;
            }

            $mrn = !empty($row['MedicalNo']) ? $row['MedicalNo'] : ($row['PatientID'] ?? '');

            $results[] = [
                'id' => $row['PatientID'] ?? null,
                'patient_id' => $row['PatientID'] ?? null,
                'medical_record_number' => $mrn,
                'name' => $name,
                'phone' => $phone,
                'email' => $row['Email'] ?? '',
                'date_of_birth' => $dob,
                'registration_no' => $row['RegistrationNo'] ?? null,
            ];
        }

        return $results;
    }

    /**
     * Get list of discharged inpatient (IPR) patients from SIM RS.
     */
    public function getDischargedInpatients(int $limit = 100, ?string $startDate = null, ?string $endDate = null): array
    {
        $dateFilter = "";
        if ($startDate && $endDate) {
            $dateFilter = "AND r.DischargeDate >= '{$startDate}' AND r.DischargeDate <= '{$endDate} 23:59:59'";
        } elseif ($startDate) {
            $dateFilter = "AND r.DischargeDate >= '{$startDate}'";
        }

        $sql = "
            SELECT TOP {$limit}
                r.RegistrationNo,
                r.PatientID,
                p.MedicalNo,
                p.FirstName,
                p.LastName,
                p.Sex,
                p.DateOfBirth,
                p.PhoneNo,
                p.MobilePhoneNo,
                r.AgeInYear,
                r.RegistrationDate,
                r.RegistrationTime,
                r.DischargeDate,
                r.DischargeTime,
                r.RoomID,
                r.BedID,
                r.ClassID,
                doc.ParamedicName as DoctorName,
                doc.Spesialisasi as DoctorSpecialty,
                r.SRDischargeCondition,
                r.SRDischargeMethod,
                r.DischargeNotes,
                r.InitialDiagnose
            FROM Registration r
            LEFT JOIN Patient p ON r.PatientID = p.PatientID
            LEFT JOIN Paramedic doc ON doc.ParamedicID = COALESCE(NULLIF(r.ParamedicIDDPJP, ''), r.ParamedicID)
            WHERE r.SRRegistrationType = 'IPR'
              AND r.DischargeDate IS NOT NULL
              AND r.IsVoid = 0
              {$dateFilter}
            ORDER BY r.DischargeDate DESC, r.DischargeTime DESC
        ";

        return $this->formatDischargedInpatients($this->query($sql));
    }

    /**
     * Helper to format discharged inpatient rows from SIM RS.
     */
    protected function formatDischargedInpatients(array $rows): array
    {
        $results = [];
        foreach ($rows as $row) {
            $firstName = trim($row['FirstName'] ?? '');
            $lastName = trim($row['LastName'] ?? '');
            $name = trim($firstName . ' ' . ($lastName !== '.' && $lastName !== '-' ? $lastName : ''));

            $phone = !empty($row['MobilePhoneNo']) ? $row['MobilePhoneNo'] : ($row['PhoneNo'] ?? '');
            $phone = preg_replace('/\D/', '', $phone);
            if (str_starts_with($phone, '62')) {
                $phone = '0' . substr($phone, 2);
            }

            $mrn = !empty($row['MedicalNo']) ? $row['MedicalNo'] : ($row['PatientID'] ?? '');

            $admissionDate = null;
            if (!empty($row['RegistrationDate'])) {
                $parts = explode(' ', $row['RegistrationDate']);
                $dateStr = $parts[0] ?? null;
                $timeStr = !empty($row['RegistrationTime']) ? trim($row['RegistrationTime']) : '00:00:00';
                $admissionDate = $dateStr ? $dateStr . ' ' . $timeStr : null;
            }

            $dischargeDate = null;
            if (!empty($row['DischargeDate'])) {
                $parts = explode(' ', $row['DischargeDate']);
                $dateStr = $parts[0] ?? null;
                $timeStr = !empty($row['DischargeTime']) ? trim($row['DischargeTime']) : '00:00:00';
                $dischargeDate = $dateStr ? $dateStr . ' ' . $timeStr : null;
            }

            $room = trim($row['RoomID'] ?? '');
            $bed = trim($row['BedID'] ?? '');
            $roomBed = trim($room . ($bed ? " (Bed: {$bed})" : ''));

            $results[] = [
                'registration_no' => $row['RegistrationNo'] ?? null,
                'patient_id' => $row['PatientID'] ?? null,
                'medical_record_number' => $mrn,
                'patient_name' => $name,
                'patient_phone' => $phone,
                'patient_age' => !empty($row['AgeInYear']) ? (int)$row['AgeInYear'] : null,
                'gender' => !empty($row['Sex']) ? strtoupper(trim($row['Sex'])) : null,
                'admission_date' => $admissionDate,
                'discharge_date' => $dischargeDate,
                'room_bed' => $roomBed,
                'doctor_dpjp' => $row['DoctorName'] ?? null,
                'doctor_specialty' => $row['DoctorSpecialty'] ?? null,
                'discharge_notes' => $row['DischargeNotes'] ?? null,
                'initial_diagnose' => $row['InitialDiagnose'] ?? null,
            ];
        }

        return $results;
    }
}

