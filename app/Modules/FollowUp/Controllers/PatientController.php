<?php

namespace App\Modules\FollowUp\Controllers;

use App\Core\Services\AuditLogService;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class PatientController extends Controller
{
    public function __construct(protected AuditLogService $auditLogService) {}

    public function index(Request $request)
    {
        $query = Patient::where('clinic_id', Auth::user()->clinic_id);

        if ($search = $request->get('search')) {
            $query->search($search);
        }

        $patients = $query->withCount('examinations')
            ->orderBy('name')
            ->paginate(config('cfms.per_page'));

        return view('follow-up.patients.index', compact('patients'));
    }

    public function create()
    {
        return view('follow-up.patients.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'medical_record_number' => 'required|string|max:50',
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'gender' => 'nullable|in:L,P',
            'date_of_birth' => 'nullable|date',
            'address' => 'nullable|string|max:500',
            'notes' => 'nullable|string|max:1000',
        ]);

        $validated['clinic_id'] = Auth::user()->clinic_id;

        $patient = Patient::create($validated);
        $this->auditLogService->logCreated('Patient', $patient->id, $validated);

        return redirect()->route('follow-up.patients.index')
            ->with('success', 'Pasien berhasil ditambahkan.');
    }

    public function show(Patient $patient)
    {
        abort_if($patient->clinic_id !== Auth::user()->clinic_id, 403);

        $patient->load([
            'examinations' => fn($q) => $q->with(['doctor', 'refractionOptician'])->latest('examination_date'),
            'followUpSchedules' => fn($q) => $q->with('latestVisit')->orderBy('scheduled_date'),
            'documentDeliveries' => fn($q) => $q->with(['documentType', 'sender'])->latest('created_at'),
            'reminders' => fn($q) => $q->with('template')->latest('created_at'),
        ]);

        // Build a unified timeline collection
        $timeline = collect();

        foreach ($patient->examinations as $exam) {
            $timeline->push([
                'type' => 'examination',
                'date' => $exam->examination_date,
                'title' => 'Pemeriksaan Awal',
                'description' => 'Diperiksa oleh ' . ($exam->doctor->name ?? '-'),
                'icon' => 'stethoscope',
                'color' => 'blue',
            ]);
        }

        foreach ($patient->followUpSchedules as $schedule) {
            if ($schedule->status === 'completed' && $schedule->latestVisit) {
                $timeline->push([
                    'type' => 'visit',
                    'date' => $schedule->latestVisit->visit_date,
                    'title' => 'Kunjungan Kontrol (' . $schedule->label . ')',
                    'description' => 'Status: Hadir',
                    'icon' => 'calendar-check',
                    'color' => 'green',
                ]);
            }
        }

        foreach ($patient->documentDeliveries as $delivery) {
            $timeline->push([
                'type' => 'email',
                'date' => $delivery->created_at,
                'title' => 'Email Terkirim',
                'description' => 'Dokumen: ' . ($delivery->documentType->name ?? '-'),
                'icon' => 'envelope',
                'color' => 'indigo',
                'status' => $delivery->status,
            ]);
        }

        foreach ($patient->reminders as $reminder) {
            $timeline->push([
                'type' => 'whatsapp',
                'date' => $reminder->created_at,
                'title' => 'WhatsApp Reminder',
                'description' => 'Pesan pengingat kontrol',
                'icon' => 'chat-bubble',
                'color' => 'teal',
                'status' => $reminder->status,
            ]);
        }

        $timeline = $timeline->sortByDesc('date')->values();

        return view('follow-up.patients.show', compact('patient', 'timeline'));
    }

    public function edit(Patient $patient)
    {
        abort_if($patient->clinic_id !== Auth::user()->clinic_id, 403);
        return view('follow-up.patients.edit', compact('patient'));
    }

    public function update(Request $request, Patient $patient)
    {
        abort_if($patient->clinic_id !== Auth::user()->clinic_id, 403);

        $validated = $request->validate([
            'medical_record_number' => 'required|string|max:50',
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'gender' => 'nullable|in:L,P',
            'date_of_birth' => 'nullable|date',
            'address' => 'nullable|string|max:500',
            'notes' => 'nullable|string|max:1000',
        ]);

        $old = $patient->toArray();
        $patient->update($validated);
        $this->auditLogService->logUpdated('Patient', $patient->id, $old, $validated);

        return redirect()->route('follow-up.patients.show', $patient)
            ->with('success', 'Data pasien berhasil diperbarui.');
    }

    public function destroy(Patient $patient)
    {
        abort_if($patient->clinic_id !== Auth::user()->clinic_id, 403);
        $old = $patient->toArray();
        $patient->delete();
        $this->auditLogService->logDeleted('Patient', $patient->id, $old);

        return redirect()->route('follow-up.patients.index')
            ->with('success', 'Pasien berhasil dihapus.');
    }

    public function deleteAll(Request $request)
    {
        $request->validate([
            'confirm_password' => 'required|string',
        ]);

        if ($request->input('confirm_password') !== 'Ikr@21983') {
            return redirect()->back()->with('error', 'Password konfirmasi salah.');
        }

        $clinicId = Auth::user()->clinic_id;

        try {
            \Illuminate\Support\Facades\DB::transaction(function () use ($clinicId) {
                // Hapus pengingat & log pengingat terkait klinik ini
                $reminderIds = \App\Models\Reminder::where('clinic_id', $clinicId)->pluck('id');
                \App\Models\ReminderLog::whereIn('reminder_id', $reminderIds)->delete();
                \App\Models\Reminder::where('clinic_id', $clinicId)->delete();

                // Force delete patients (akan men-trigger database ON DELETE CASCADE untuk:
                // examinations, follow_up_schedules, follow_up_visits, document_deliveries, processed_documents)
                \App\Models\Patient::where('clinic_id', $clinicId)->forceDelete();
            });

            $this->auditLogService->logDeleted('Patient', null, ['description' => 'Menghapus seluruh master data pasien dan data transaksi terkait.']);

            return redirect()->route('follow-up.patients.index')
                ->with('success', 'Semua data pasien dan transaksi terkait berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }
}
