<?php

namespace App\Modules\FollowUp\Services;

use App\Core\Services\AuditLogService;
use App\Models\Examination;
use App\Models\FollowUpSchedule;
use App\Models\FollowUpVisit;
use App\Models\Reminder;
use App\Models\ReminderTemplate;
use App\Modules\FollowUp\Events\ExaminationCreated;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class FollowUpService
{
    public function __construct(
        protected AuditLogService $auditLogService
    ) {}

    /**
     * Create a new examination and trigger follow-up schedule generation.
     */
    public function createExamination(array $data): Examination
    {
        return DB::transaction(function () use ($data) {
            $examination = Examination::create($data);

            // Dispatch event to generate follow-up schedules
            event(new ExaminationCreated($examination));

            $this->auditLogService->logCreated(
                'Examination',
                $examination->id,
                $data
            );

            return $examination->load(['patient', 'doctor', 'refractionOptician', 'followUpSchedules']);
        });
    }

    /**
     * Record a follow-up visit.
     */
    public function recordVisit(FollowUpSchedule $schedule, array $data): FollowUpVisit
    {
        return DB::transaction(function () use ($schedule, $data) {
            $visit = FollowUpVisit::create(array_merge($data, [
                'follow_up_schedule_id' => $schedule->id,
                'examination_id' => $schedule->examination_id,
                'patient_id' => $schedule->patient_id,
                'clinic_id' => $schedule->clinic_id,
            ]));

            // Update schedule status based on visit status
            $statusSlug = $visit->followUpStatus->slug ?? 'hadir';

            match ($statusSlug) {
                'hadir' => $schedule->update(['status' => 'completed']),
                'tidak-hadir' => $schedule->update(['status' => 'missed']),
                'reschedule' => $schedule->update([
                    'status' => 'rescheduled',
                    'rescheduled_date' => $data['rescheduled_to'] ?? null,
                ]),
                default => null,
            };

            $this->auditLogService->logCreated('FollowUpVisit', $visit->id, $data);

            return $visit->load(['followUpStatus', 'lensCondition']);
        });
    }

    /**
     * Get dashboard statistics.
     */
    public function getDashboardStats(int $clinicId): array
    {
        $today = Carbon::today();

        return [
            'total_patients' => FollowUpSchedule::where('clinic_id', $clinicId)
                ->distinct('patient_id')->count('patient_id'),

            'due_today' => FollowUpSchedule::where('clinic_id', $clinicId)
                ->dueToday()->count(),

            'due_this_week' => FollowUpSchedule::where('clinic_id', $clinicId)
                ->dueThisWeek()->count(),

            'overdue' => FollowUpSchedule::where('clinic_id', $clinicId)
                ->overdue()->count(),

            'missed' => FollowUpSchedule::where('clinic_id', $clinicId)
                ->where('status', 'missed')
                ->whereDate('scheduled_date', '>=', $today->copy()->subDays(30))
                ->count(),

            'completed_this_month' => FollowUpSchedule::where('clinic_id', $clinicId)
                ->where('status', 'completed')
                ->whereMonth('updated_at', $today->month)
                ->whereYear('updated_at', $today->year)
                ->count(),

            'reminders_sent' => Reminder::where('clinic_id', $clinicId)
                ->where('status', 'sent')
                ->whereMonth('sent_at', $today->month)
                ->count(),

            'reminders_failed' => Reminder::where('clinic_id', $clinicId)
                ->where('status', 'failed')
                ->whereMonth('created_at', $today->month)
                ->count(),
        ];
    }

    /**
     * Get follow-up schedules with filters.
     */
    public function getSchedules(int $clinicId, array $filters = []): LengthAwarePaginator
    {
        $query = FollowUpSchedule::with(['patient', 'examination.doctor', 'latestVisit'])
            ->where('clinic_id', $clinicId);

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['date_from'])) {
            $query->whereDate('scheduled_date', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('scheduled_date', '<=', $filters['date_to']);
        }

        if (!empty($filters['search'])) {
            $query->whereHas('patient', function ($q) use ($filters) {
                $q->where('name', 'LIKE', "%{$filters['search']}%")
                  ->orWhere('medical_record_number', 'LIKE', "%{$filters['search']}%");
            });
        }

        $sortBy = $filters['sort'] ?? 'scheduled_date';
        $sortDir = $filters['direction'] ?? 'asc';

        return $query->orderBy($sortBy, $sortDir)
                     ->paginate($filters['per_page'] ?? config('cfms.per_page'));
    }

    /**
     * Create reminders for upcoming follow-ups.
     */
    public function createRemindersForSchedule(FollowUpSchedule $schedule): void
    {
        $examination = $schedule->examination->load(['patient', 'doctor', 'refractionOptician']);
        $patient = $examination->patient;
        $template = ReminderTemplate::where('type', 'follow_up')
            ->where('is_default', true)
            ->where('is_active', true)
            ->first();

        if (!$template || !$patient->phone) {
            return;
        }

        $variables = [
            'patient_name' => $patient->name,
            'clinic_name' => $schedule->clinic->name ?? config('cfms.name'),
            'scheduled_date' => $schedule->effective_date->format('d M Y'),
            'doctor_name' => $examination->doctor->name ?? '-',
            'follow_up_label' => $schedule->label,
            'medical_record_number' => $patient->medical_record_number,
        ];

        // Reminder for patient
        Reminder::create([
            'clinic_id' => $schedule->clinic_id,
            'follow_up_schedule_id' => $schedule->id,
            'reminder_template_id' => $template->id,
            'channel' => 'whatsapp',
            'recipient_type' => 'patient',
            'recipient_name' => $patient->name,
            'recipient_phone' => $patient->phone,
            'message' => $template->parse($variables),
            'status' => 'pending',
            'scheduled_at' => $schedule->effective_date->subHours(config('cfms.reminder.hours_before', 24)),
        ]);

        $schedule->update([
            'reminder_sent' => true,
            'reminder_sent_at' => now(),
        ]);
    }
}
