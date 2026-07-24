<?php

namespace App\Core\Services;

use App\Models\Patient;
use Illuminate\Support\Facades\Auth;

class PatientRegistrationService
{
    /**
     * Register or update a patient based on deduplication rules.
     *
     * @param array $data
     * @return Patient
     */
    public function register(array $data): Patient
    {
        $clinicId = $data['clinic_id'] ?? (Auth::check() ? Auth::user()->clinic_id : null);
        
        if (!$clinicId) {
            throw new \InvalidArgumentException('Clinic ID is required for patient registration.');
        }

        $name = trim($data['name']);
        $phone = isset($data['phone']) ? trim($data['phone']) : null;
        $nik = isset($data['nik']) ? trim($data['nik']) : null;
        $dob = isset($data['date_of_birth']) ? $data['date_of_birth'] : null;
        $gender = $data['gender'] ?? null;
        $email = $data['email'] ?? null;
        $address = $data['address'] ?? null;
        $notes = $data['notes'] ?? null;
        
        $source = $data['registration_source'] ?? 'admin';
        $sourceId = isset($data['registration_source_id']) && is_numeric($data['registration_source_id']) 
            ? (int) $data['registration_source_id'] 
            : null;

        $patient = null;
        $mrn = $data['medical_record_number'] ?? null;

        // 0. Try to deduplicate by Medical Record Number (MRN) if provided
        if (!empty($mrn)) {
            $patient = Patient::where('clinic_id', $clinicId)
                ->where('medical_record_number', $mrn)
                ->first();
        }

        // 1. Try to deduplicate by NIK (if provided)
        if (!$patient && !empty($nik)) {
            $patient = Patient::where('clinic_id', $clinicId)
                ->where('nik', $nik)
                ->first();
        }

        // 2. Try to deduplicate by Name + Phone + DOB (if phone is provided)
        if (!$patient && !empty($phone)) {
            $query = Patient::where('clinic_id', $clinicId)
                ->where('name', 'LIKE', $name)
                ->where('phone', $phone);
                
            if ($dob) {
                $query->whereDate('date_of_birth', $dob);
            }
            
            $patient = $query->first();
        }

        // 3. Fallback: Deduplicate by Name only (if no NIK and no Phone, e.g. imported downtime)
        if (!$patient && empty($nik) && empty($phone)) {
            $patient = Patient::where('clinic_id', $clinicId)
                ->where('name', 'LIKE', $name)
                ->first();
        }

        if ($patient) {
            // Update existing patient with new data if existing was empty
            $updates = [];
            if (empty($patient->nik) && !empty($nik)) $updates['nik'] = $nik;
            if (empty($patient->phone) && !empty($phone)) $updates['phone'] = $phone;
            if (empty($patient->email) && !empty($email)) $updates['email'] = $email;
            if (empty($patient->date_of_birth) && !empty($dob)) $updates['date_of_birth'] = $dob;
            if (empty($patient->gender) && !empty($gender)) $updates['gender'] = $gender;
            if (empty($patient->address) && !empty($address)) $updates['address'] = $address;
            if (empty($patient->notes) && !empty($notes)) $updates['notes'] = $notes;

            // Always update registration source to track the latest campaign/event interaction
            $updates['registration_source'] = $source;
            $updates['registration_source_id'] = $sourceId;

            if ($source === 'downtime') {
                $updates['is_downtime_entry'] = true;
            }

            $patient->update($updates);
            return $patient;
        }

        // 4. Create new patient
        $mrn = $data['medical_record_number'] ?? null;
        if (!$mrn) {
            if (in_array($source, ['downtime', 'event', 'marketing'])) {
                // Generate temporary RM number
                $mrn = 'TEMP-' . date('Ymd') . '-' . rand(100000, 999999);
                while (Patient::where('clinic_id', $clinicId)->where('medical_record_number', $mrn)->exists()) {
                    $mrn = 'TEMP-' . date('Ymd') . '-' . rand(100000, 999999);
                }
            } else {
                // Generate normal RM number
                $mrn = 'RM-' . date('Ymd') . '-' . rand(1000, 9999);
                while (Patient::where('clinic_id', $clinicId)->where('medical_record_number', $mrn)->exists()) {
                    $mrn = 'RM-' . date('Ymd') . '-' . rand(1000, 9999);
                }
            }
        }

        return Patient::create([
            'clinic_id' => $clinicId,
            'medical_record_number' => $mrn,
            'name' => $name,
            'nik' => $nik,
            'phone' => $phone,
            'email' => $email,
            'gender' => $gender,
            'date_of_birth' => $dob,
            'address' => $address,
            'notes' => $notes,
            'is_active' => true,
            'registration_source' => $source,
            'registration_source_id' => $sourceId,
            'is_downtime_entry' => ($source === 'downtime'),
            'parent_spouse_name' => $data['parent_spouse_name'] ?? null,
            'emergency_contact_name' => $data['emergency_contact_name'] ?? null,
            'emergency_contact_phone' => $data['emergency_contact_phone'] ?? null,
        ]);
    }
}
