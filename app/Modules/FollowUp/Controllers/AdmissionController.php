<?php

namespace App\Modules\FollowUp\Controllers;

use App\Models\Patient;
use App\Core\Services\AuditLogService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class AdmissionController extends Controller
{
    public function __construct(protected AuditLogService $auditLogService) {}

    public function scanView()
    {
        // Get patients who arrived today at the hospital for current clinic
        $arrivedToday = Patient::where('clinic_id', Auth::user()->clinic_id)
            ->where('registration_source', 'event')
            ->whereNotNull('hospital_arrival_at')
            ->whereDate('hospital_arrival_at', today())
            ->with('event')
            ->orderBy('hospital_arrival_at', 'desc')
            ->get();

        return view('admission.scan', compact('arrivedToday'));
    }

    public function checkIn(Request $request)
    {
        $request->validate([
            'barcode' => 'required|string|max:100',
        ]);

        $clinicId = Auth::user()->clinic_id;
        $barcode = trim($request->input('barcode'));

        // Find patient by medical_record_number (or temporary_medical_record_number)
        $patient = Patient::where('clinic_id', $clinicId)
            ->where(function($q) use ($barcode) {
                $q->where('medical_record_number', $barcode)
                  ->orWhere('temporary_medical_record_number', $barcode);
            })
            ->with('event')
            ->first();

        if (!$patient) {
            return response()->json([
                'success' => false,
                'message' => "Pasien dengan kode '{$barcode}' tidak ditemukan.",
            ], 404);
        }

        // Check if the patient registration source is 'event'
        if ($patient->registration_source !== 'event') {
            return response()->json([
                'success' => false,
                'message' => "Pasien '{$patient->name}' terdaftar bukan melalui Event.",
            ], 400);
        }

        $alreadyCheckedIn = $patient->hospital_arrival_at !== null;
        $oldArrival = $patient->hospital_arrival_at;

        if (!$alreadyCheckedIn) {
            $patient->update([
                'hospital_arrival_at' => now(),
            ]);

            $this->auditLogService->logUpdated('Patient', $patient->id, 
                ['hospital_arrival_at' => null], 
                ['hospital_arrival_at' => $patient->hospital_arrival_at]
            );
        }

        return response()->json([
            'success' => true,
            'message' => $alreadyCheckedIn 
                ? "Pasien '{$patient->name}' sudah check-in sebelumnya pada " . $oldArrival->timezone(config('app.timezone', 'Asia/Makassar'))->format('H:i') . " WITA."
                : "Check-in sukses untuk pasien '{$patient->name}'.",
            'patient' => [
                'id' => $patient->id,
                'name' => $patient->name,
                'medical_record_number' => $patient->medical_record_number,
                'phone' => $patient->phone,
                'gender' => $patient->gender == 'L' ? 'Laki-laki' : 'Perempuan',
                'event_name' => $patient->event ? $patient->event->name : '-',
                'hospital_arrival_at' => $patient->hospital_arrival_at->timezone(config('app.timezone', 'Asia/Makassar'))->format('d M Y H:i') . " WITA",
                'already_checked_in' => $alreadyCheckedIn,
            ]
        ]);
    }
}
