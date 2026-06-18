<?php

namespace App\Modules\FollowUp\Listeners;

use App\Models\FollowUpSchedule;
use App\Modules\FollowUp\Events\ExaminationCreated;

class GenerateFollowUpSchedule
{
    public function handle(ExaminationCreated $event): void
    {
        $examination = $event->examination;
        $intervals = config('cfms.follow_up_intervals');

        foreach ($intervals as $sequence => $interval) {
            FollowUpSchedule::create([
                'examination_id' => $examination->id,
                'patient_id' => $examination->patient_id,
                'clinic_id' => $examination->clinic_id,
                'label' => $interval['label'],
                'interval_days' => $interval['days'],
                'scheduled_date' => $examination->examination_date->addDays($interval['days']),
                'sequence' => $sequence + 1,
                'status' => 'pending',
            ]);
        }
    }
}
