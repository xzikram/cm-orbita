<?php

namespace App\Modules\FollowUp\Controllers;

use App\Core\Services\AuditLogService;
use App\Core\Services\SimrsBridgeService;
use App\Models\InpatientFollowUp;
use App\Models\Patient;
use App\Modules\Reminder\Contracts\WhatsAppProviderInterface;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class InpatientFollowUpController extends Controller
{
    public function __construct(
        protected SimrsBridgeService $simrsBridgeService,
        protected WhatsAppProviderInterface $whatsAppProvider,
        protected AuditLogService $auditLogService
    ) {}

    public function index(Request $request)
    {
        $clinicId = Auth::user()->clinic_id;

        $query = InpatientFollowUp::where('clinic_id', $clinicId)
            ->with(['sentBy', 'respondedBy']);

        // Filter status / tab
        $tab = $request->get('status', 'all');
        if ($tab === 'due_today') {
            $query->dueToday();
        } elseif ($tab === 'pending') {
            $query->pending();
        } elseif ($tab === 'sent') {
            $query->sent();
        } elseif ($tab === 'completed') {
            $query->completed();
        } elseif ($tab === 'overdue') {
            $query->overdue();
        } elseif ($tab === 'needs_review') {
            $query->where('needs_doctor_review', true);
        }

        // Search term
        if ($search = trim($request->get('search', ''))) {
            $query->where(function ($q) use ($search) {
                $q->where('patient_name', 'LIKE', "%{$search}%")
                  ->orWhere('medical_record_number', 'LIKE', "%{$search}%")
                  ->orWhere('registration_no', 'LIKE', "%{$search}%")
                  ->orWhere('doctor_dpjp', 'LIKE', "%{$search}%")
                  ->orWhere('room_bed', 'LIKE', "%{$search}%");
            });
        }

        // Date range filters
        if ($dateFrom = $request->get('date_from')) {
            $query->whereDate('discharge_date', '>=', $dateFrom);
        }
        if ($dateTo = $request->get('date_to')) {
            $query->whereDate('discharge_date', '<=', $dateTo);
        }

        $sort = $request->get('sort', 'discharge_date');
        $direction = $request->get('direction', 'desc');
        $followUps = $query->orderBy($sort, $direction)->paginate(20);

        // Status counts for badges
        $statusCounts = [
            'all' => InpatientFollowUp::where('clinic_id', $clinicId)->count(),
            'due_today' => InpatientFollowUp::where('clinic_id', $clinicId)->dueToday()->count(),
            'pending' => InpatientFollowUp::where('clinic_id', $clinicId)->pending()->count(),
            'sent' => InpatientFollowUp::where('clinic_id', $clinicId)->sent()->count(),
            'completed' => InpatientFollowUp::where('clinic_id', $clinicId)->completed()->count(),
            'overdue' => InpatientFollowUp::where('clinic_id', $clinicId)->overdue()->count(),
            'needs_review' => InpatientFollowUp::where('clinic_id', $clinicId)->where('needs_doctor_review', true)->count(),
        ];

        return view('follow-up.inpatient.index', compact('followUps', 'statusCounts', 'tab'));
    }

    public function create()
    {
        return view('follow-up.inpatient.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'medical_record_number' => 'required|string|max:50',
            'patient_name' => 'required|string|max:255',
            'patient_phone' => 'nullable|string|max:30',
            'patient_age' => 'nullable|integer|min:0|max:150',
            'gender' => 'nullable|in:L,P,M,F',
            'admission_date' => 'nullable|date',
            'discharge_date' => 'required|date',
            'room_bed' => 'nullable|string|max:100',
            'doctor_dpjp' => 'nullable|string|max:255',
            'diagnosis_or_procedure' => 'nullable|string|max:500',
            'registration_no' => 'nullable|string|max:100',
        ]);

        $clinicId = Auth::user()->clinic_id;

        // Auto calculate follow-up due date = discharge_date + 3 days
        $discharge = Carbon::parse($validated['discharge_date']);
        $dueDate = $discharge->copy()->addDays(3)->toDateString();

        // Check if patient exists in local patients table
        $patient = Patient::where('clinic_id', $clinicId)
            ->where('medical_record_number', $validated['medical_record_number'])
            ->first();

        $followUp = InpatientFollowUp::create([
            'clinic_id' => $clinicId,
            'patient_id' => $patient?->id,
            'registration_no' => $validated['registration_no'] ?? ('MANUAL/' . date('ymd') . '-' . strtoupper(substr(uniqid(), -4))),
            'medical_record_number' => $validated['medical_record_number'],
            'patient_name' => strtoupper($validated['patient_name']),
            'patient_phone' => $validated['patient_phone'],
            'patient_age' => $validated['patient_age'],
            'gender' => $validated['gender'] ? strtoupper($validated['gender']) : null,
            'admission_date' => $validated['admission_date'] ? Carbon::parse($validated['admission_date']) : null,
            'discharge_date' => $discharge,
            'follow_up_due_date' => $dueDate,
            'room_bed' => $validated['room_bed'],
            'doctor_dpjp' => $validated['doctor_dpjp'],
            'diagnosis_or_procedure' => $validated['diagnosis_or_procedure'],
            'status' => 'pending',
            'source' => 'manual',
        ]);

        $this->auditLogService->logCreated('InpatientFollowUp', $followUp->id, $followUp->toArray());

        return redirect()->route('follow-up.inpatient.index')
            ->with('success', "Pasien rawat inap {$followUp->patient_name} berhasil ditambahkan manual. Jadwal follow-up H+3 diset ke " . Carbon::parse($dueDate)->format('d M Y') . ".");
    }

    /**
     * Sync discharged inpatients from SIM RS.
     */
    public function syncFromSimrs(Request $request)
    {
        $clinicId = Auth::user()->clinic_id;
        $limit = (int)$request->get('limit', 100);

        try {
            $simrsPatients = $this->simrsBridgeService->getDischargedInpatients($limit);
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal terhubung ke SIM RS: ' . $e->getMessage());
        }

        if (empty($simrsPatients)) {
            return back()->with('info', 'Tidak ada data pasien pulang rawat inap baru dari SIM RS.');
        }

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

            $dueDate = $dischargeDate->copy()->addDays(3)->toDateString();

            // Link to local patient if exists
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

        $this->auditLogService->logCreated('InpatientFollowUpSync', 0, [
            'synced_count' => $createdCount,
            'total_fetched' => count($simrsPatients),
        ]);

        return redirect()->route('follow-up.inpatient.index')
            ->with('success', "Sinkronisasi berhasil: {$createdCount} data pasien pulang rawat inap baru berhasil dimasukkan ke antrean follow-up H+3.");
    }

    /**
     * Send WhatsApp follow-up message to the patient.
     */
    public function sendWhatsApp(InpatientFollowUp $followUp, Request $request)
    {
        abort_if($followUp->clinic_id !== Auth::user()->clinic_id, 403);

        $nurseName = trim($request->input('nurse_name') ?: (Auth::user()->name ?: 'Perawat Rawat Inap'));
        $recipientPhone = trim($request->input('patient_phone') ?: $followUp->formatted_phone);

        if (empty($recipientPhone)) {
            return back()->with('error', 'Nomor WhatsApp pasien tidak tersedia.');
        }

        // Time greeting (Pagi / Siang / Sore / Malam)
        $hour = (int)now()->timezone(config('app.timezone', 'Asia/Makassar'))->format('H');
        $greetingTime = match (true) {
            $hour >= 5 && $hour < 11 => 'Pagi',
            $hour >= 11 && $hour < 15 => 'Siang',
            $hour >= 15 && $hour < 19 => 'Sore',
            default => 'Malam',
        };

        $patientTitle = ($followUp->gender === 'P' || $followUp->gender === 'F') ? 'Ibu' : 'Bapak';
        $patientAgeText = $followUp->patient_age ? " ({$followUp->patient_age} tahun)" : "";

        // Build exact message format requested
        $message = "Selamat {$greetingTime} {$patientTitle}.\n\n"
            . "\"Perkenalkan, saya {$nurseName} Perawat Rawat Inap RS Mata JEC Orbita Makassar. Kami ingin melakukan tindak lanjut (follow up) terkait kondisi pasien atas nama {$followUp->patient_name}{$patientAgeText} setelah menjalani perawatan di rumah sakit.\n\n"
            . "Mohon bantuannya untuk menginformasikan:\n\n"
            . "1. Apakah saat ini pasien masih memiliki keluhan?\n"
            . "2. Apakah obat yang diberikan masih digunakan sesuai anjuran dokter?\n"
            . "3. Apakah terdapat efek samping atau reaksi yang tidak biasa setelah menggunakan obat?\n"
            . "4. Bagaimana kondisi luka operasi saat ini? Apakah ada kemerahan, bengkak, keluar cairan, atau keluhan lainnya?\n"
            . "5. Apakah terdapat perubahan atau kemajuan pada penglihatan pasien?\n\n"
            . "Mohon konfirmasinya apabila pesan ini telah diterima. Terima kasih atas kerja sama {$patientTitle}.\n\n"
            . "Salam sehat,\n\n"
            . "{$nurseName}\n"
            . "Perawat Rawat Inap\n"
            . "RS Mata JEC Orbita Makassar\"";

        try {
            $result = $this->whatsAppProvider->sendMessage($recipientPhone, $message);

            if ($result->success) {
                $followUp->update([
                    'status' => 'sent',
                    'nurse_name' => $nurseName,
                    'patient_phone' => $recipientPhone,
                    'whatsapp_message' => $message,
                    'whatsapp_message_id' => $result->messageId,
                    'sent_at' => now(),
                    'sent_by' => Auth::id(),
                ]);

                $this->auditLogService->logUpdated('InpatientFollowUp', $followUp->id, ['status' => 'pending'], ['status' => 'sent', 'nurse_name' => $nurseName]);

                return redirect()->route('follow-up.inpatient.index')
                    ->with('success', "Pesan follow-up rawat inap berhasil dikirimkan via WhatsApp ke {$followUp->patient_name}.");
            }

            return back()->with('error', 'Gagal mengirim pesan WhatsApp: ' . ($result->error ?? 'Kesalahan pada WhatsApp Gateway'));
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Show form to record patient clinical responses.
     */
    public function recordResponseView(InpatientFollowUp $followUp)
    {
        abort_if($followUp->clinic_id !== Auth::user()->clinic_id, 403);

        return view('follow-up.inpatient.record-response', compact('followUp'));
    }

    /**
     * Store patient clinical responses.
     */
    public function storeResponse(InpatientFollowUp $followUp, Request $request)
    {
        abort_if($followUp->clinic_id !== Auth::user()->clinic_id, 403);

        $validated = $request->validate([
            'response_complaints' => 'nullable|string|max:1000',
            'response_medication_compliance' => 'required|in:patuh,tidak_patuh,sebagian',
            'response_side_effects' => 'nullable|string|max:1000',
            'response_wound_condition' => 'required|in:baik_kering,merah_bengkak,keluar_cairan,nyeri_hebat',
            'response_vision_progress' => 'required|in:membaik,tetap,menurun',
            'response_notes' => 'nullable|string|max:1000',
            'needs_doctor_review' => 'nullable|boolean',
        ]);

        $needsReview = $request->boolean('needs_doctor_review') 
            || in_array($validated['response_wound_condition'], ['merah_bengkak', 'keluar_cairan', 'nyeri_hebat'])
            || $validated['response_vision_progress'] === 'menurun';

        $followUp->update([
            'response_complaints' => $validated['response_complaints'],
            'response_medication_compliance' => $validated['response_medication_compliance'],
            'response_side_effects' => $validated['response_side_effects'],
            'response_wound_condition' => $validated['response_wound_condition'],
            'response_vision_progress' => $validated['response_vision_progress'],
            'response_notes' => $validated['response_notes'],
            'needs_doctor_review' => $needsReview,
            'status' => 'completed',
            'responded_at' => now(),
            'responded_by' => Auth::id(),
        ]);

        $this->auditLogService->logUpdated('InpatientFollowUp', $followUp->id, ['status' => 'sent'], ['status' => 'completed', 'needs_doctor_review' => $needsReview]);

        $msg = $needsReview 
            ? "Respon follow-up pasien {$followUp->patient_name} berhasil disimpan. PERHATIAN: Pasien ditandai memerlukan evaluasi/perhatian DPJP."
            : "Respon follow-up pasien {$followUp->patient_name} berhasil disimpan dengan status Selesai.";

        return redirect()->route('follow-up.inpatient.index')->with('success', $msg);
    }

    /**
     * Delete an inpatient follow up record.
     */
    public function destroy(InpatientFollowUp $followUp)
    {
        abort_if($followUp->clinic_id !== Auth::user()->clinic_id, 403);

        $patientName = $followUp->patient_name;
        $followUp->delete();

        $this->auditLogService->logDeleted('InpatientFollowUp', $followUp->id, ['patient_name' => $patientName]);

        return redirect()->route('follow-up.inpatient.index')
            ->with('success', "Data follow-up rawat inap untuk {$patientName} berhasil dihapus.");
    }
}
