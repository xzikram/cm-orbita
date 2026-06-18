<?php

namespace App\Modules\Dashboard\Controllers;

use App\Modules\FollowUp\Services\FollowUpService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function __construct(
        protected FollowUpService $followUpService
    ) {}

    public function index(Request $request)
    {
        $clinicId = Auth::user()->clinic_id;

        $stats = $this->followUpService->getDashboardStats($clinicId);

        // Get today's schedules
        $todaySchedules = \App\Models\FollowUpSchedule::with(['patient', 'examination.doctor'])
            ->where('clinic_id', $clinicId)
            ->dueToday()
            ->limit(10)
            ->get();

        // Get overdue schedules
        $overdueSchedules = \App\Models\FollowUpSchedule::with(['patient', 'examination.doctor'])
            ->where('clinic_id', $clinicId)
            ->overdue()
            ->limit(10)
            ->get();

        // Get upcoming schedules (next 7 days)
        $upcomingSchedules = \App\Models\FollowUpSchedule::with(['patient', 'examination.doctor'])
            ->where('clinic_id', $clinicId)
            ->upcoming()
            ->whereDate('scheduled_date', '<=', now()->addDays(7))
            ->limit(10)
            ->get();

        return view('dashboard.index', compact(
            'stats', 'todaySchedules', 'overdueSchedules', 'upcomingSchedules'
        ));
    }
}
