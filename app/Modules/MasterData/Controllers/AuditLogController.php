<?php

namespace App\Modules\MasterData\Controllers;

use App\Models\AuditLog;
use App\Models\Reminder;
use App\Models\ReminderLog;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        $query = AuditLog::with('user')->latest();

        if ($action = $request->get('action')) {
            $query->forAction($action);
        }

        if ($userId = $request->get('user_id')) {
            $query->forUser($userId);
        }

        if ($search = $request->get('search')) {
            $query->where('description', 'LIKE', "%{$search}%");
        }

        $logs = $query->paginate(config('cfms.per_page'));

        return view('audit.index', compact('logs'));
    }

    public function deletionLogs(Request $request)
    {
        $query = \App\Models\DeletionLog::with('user')->latest();

        if ($search = $request->get('search')) {
            $query->where('model_name', 'LIKE', "%{$search}%")
                ->orWhere('model_identifier', 'LIKE', "%{$search}%")
                ->orWhere('reason', 'LIKE', "%{$search}%");
        }

        $logs = $query->paginate(config('cfms.per_page', 10));

        return view('audit.deletion_logs', compact('logs'));
    }
}


