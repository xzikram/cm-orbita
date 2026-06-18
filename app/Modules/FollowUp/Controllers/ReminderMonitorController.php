<?php

namespace App\Modules\FollowUp\Controllers;

use App\Models\Reminder;
use App\Models\ReminderLog;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class ReminderMonitorController extends Controller
{
    public function index(Request $request)
    {
        $query = Reminder::with(['patient', 'schedule'])
            ->where('clinic_id', Auth::user()->clinic_id);

        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        $reminders = $query->latest('scheduled_at')
            ->paginate(config('cfms.per_page'));

        return view('follow-up.reminders.index', compact('reminders'));
    }

    public function logs(Request $request)
    {
        $logs = ReminderLog::with('reminder.patient')
            ->latest()
            ->paginate(config('cfms.per_page', 15));

        return view('follow-up.reminders.logs', compact('logs'));
    }
}
