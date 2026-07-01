<?php

namespace App\Modules\FollowUp\Controllers;

use App\Models\FollowUpSchedule;
use App\Models\FollowUpStatus;
use App\Models\LensCondition;
use App\Modules\FollowUp\Services\FollowUpService;
use App\Modules\Reminder\Services\ReminderService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class FollowUpScheduleController extends Controller
{
    public function __construct(protected FollowUpService $followUpService) {}

    public function index(Request $request)
    {
        $clinicId = Auth::user()->clinic_id;

        $schedules = $this->followUpService->getSchedules($clinicId, [
            'status' => $request->get('status'),
            'search' => $request->get('search'),
            'date_from' => $request->get('date_from'),
            'date_to' => $request->get('date_to'),
            'sort' => $request->get('sort', 'scheduled_date'),
            'direction' => $request->get('direction', 'asc'),
        ]);

        $statusCounts = [
            'all' => FollowUpSchedule::where('clinic_id', $clinicId)->count(),
            'pending' => FollowUpSchedule::where('clinic_id', $clinicId)->where('status', 'pending')->count(),
            'completed' => FollowUpSchedule::where('clinic_id', $clinicId)->where('status', 'completed')->count(),
            'missed' => FollowUpSchedule::where('clinic_id', $clinicId)->where('status', 'missed')->count(),
            'overdue' => FollowUpSchedule::where('clinic_id', $clinicId)->overdue()->count(),
        ];

        return view('follow-up.schedules.index', compact('schedules', 'statusCounts'));
    }

    public function recordVisit(FollowUpSchedule $schedule)
    {
        abort_if($schedule->clinic_id !== Auth::user()->clinic_id, 403);

        $schedule->load(['patient', 'examination.doctor']);
        $statuses = FollowUpStatus::active()->get();
        $lensConditions = LensCondition::active()->get();

        return view('follow-up.schedules.record-visit', compact('schedule', 'statuses', 'lensConditions'));
    }

    public function storeVisit(Request $request, FollowUpSchedule $schedule)
    {
        abort_if($schedule->clinic_id !== Auth::user()->clinic_id, 403);

        $validated = $request->validate([
            'visit_date' => 'required|date',
            'visus_od' => 'nullable|string|max:20',
            'visus_os' => 'nullable|string|max:20',
            'complaints' => 'nullable|string|max:2000',
            'lens_condition_id' => 'nullable|exists:lens_conditions,id',
            'lens_condition_notes' => 'nullable|string|max:500',
            'doctor_notes' => 'nullable|string|max:2000',
            'follow_up_status_id' => 'required|exists:follow_up_statuses,id',
            'rescheduled_to' => 'nullable|date|after:today',
            'reschedule_reason' => 'nullable|string|max:500',
        ]);

        $validated['examined_by'] = Auth::id();

        $this->followUpService->recordVisit($schedule, $validated);

        return redirect()->route('follow-up.schedules.index')
            ->with('success', 'Hasil kontrol berhasil disimpan.');
    }

    public function sendReminder(FollowUpSchedule $schedule, ReminderService $reminderService)
    {
        abort_if($schedule->clinic_id !== Auth::user()->clinic_id, 403);

        if (empty($schedule->patient->phone)) {
            return back()->with('error', 'Pasien tidak memiliki nomor telepon terdaftar.');
        }

        try {
            // 1. Buat data reminder di tabel reminders
            $this->followUpService->createRemindersForSchedule($schedule);
            
            // 2. Temukan data reminder yang baru saja dibuat
            $reminder = \App\Models\Reminder::where('follow_up_schedule_id', $schedule->id)
                ->where('status', 'pending')
                ->latest()
                ->first();

            if (!$reminder) {
                return back()->with('error', 'Gagal membuat pengingat. Pastikan template default untuk WhatsApp aktif telah dikonfigurasi.');
            }

            // 3. Kirim pesan saat ini juga
            $result = $reminderService->send($reminder);

            if ($result->success) {
                return back()->with('success', 'Reminder WhatsApp berhasil dikirim ke ' . $schedule->patient->name);
            }

            return back()->with('error', 'Gagal mengirim WhatsApp: ' . ($result->error ?? 'Kesalahan tidak diketahui'));
        } catch (\Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }
}
