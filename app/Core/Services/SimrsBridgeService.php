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
     * Search patients in SIM RS by term (Name or PatientID/MRN).
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
                p.FirstName, 
                p.LastName, 
                p.DateOfBirth, 
                p.PhoneNo, 
                p.MobilePhoneNo, 
                p.Email
            FROM Patient p
            WHERE p.PatientID LIKE '%{$cleanTerm}%'
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

            $results[] = [
                'id' => $row['PatientID'] ?? null,
                'patient_id' => $row['PatientID'] ?? null,
                'medical_record_number' => $row['PatientID'] ?? null,
                'name' => $name,
                'phone' => $phone,
                'email' => $row['Email'] ?? '',
                'date_of_birth' => $dob,
                'registration_no' => $row['RegistrationNo'] ?? null,
            ];
        }

        return $results;
    }
}
