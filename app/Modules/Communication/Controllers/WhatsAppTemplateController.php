<?php

namespace App\Modules\Communication\Controllers;

use App\Core\Services\AuditLogService;
use App\Models\ReminderTemplate;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class WhatsAppTemplateController extends Controller
{
    public function __construct(protected AuditLogService $auditLogService) {}

    public function index()
    {
        $templates = ReminderTemplate::where('clinic_id', Auth::user()->clinic_id)
            ->where('channel', 'whatsapp')
            ->latest()
            ->paginate(config('cfms.per_page'));

        return view('communication.whatsapp-templates.index', compact('templates'));
    }

    public function create()
    {
        return view('communication.whatsapp-templates.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|in:follow_up,appointment,custom',
            'content' => 'required|string',
            'is_default' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['clinic_id'] = Auth::user()->clinic_id;
        $validated['channel'] = 'whatsapp';
        $validated['is_default'] = $request->boolean('is_default');
        $validated['is_active'] = $request->has('is_active') ? $request->boolean('is_active') : true;

        // Parse variables in content (e.g. {patient_name})
        preg_match_all('/\{([^}]+)\}/', $validated['content'], $matches);
        $validated['variables'] = array_unique($matches[1] ?? []);

        // If is_default is true, remove default status from other templates of same type
        if ($validated['is_default']) {
            ReminderTemplate::where('clinic_id', $validated['clinic_id'])
                ->where('type', $validated['type'])
                ->where('channel', 'whatsapp')
                ->update(['is_default' => false]);
        }

        $template = ReminderTemplate::create($validated);
        $this->auditLogService->logCreated('ReminderTemplate', $template->id, ['name' => $template->name, 'type' => $template->type]);

        return redirect()->route('communication.whatsapp-templates.index')
            ->with('success', 'WhatsApp Template berhasil dibuat.');
    }

    public function edit(ReminderTemplate $whatsappTemplate)
    {
        abort_if($whatsappTemplate->clinic_id !== Auth::user()->clinic_id, 403);
        return view('communication.whatsapp-templates.edit', compact('whatsappTemplate'));
    }

    public function update(Request $request, ReminderTemplate $whatsappTemplate)
    {
        abort_if($whatsappTemplate->clinic_id !== Auth::user()->clinic_id, 403);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|in:follow_up,appointment,custom',
            'content' => 'required|string',
            'is_default' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_default'] = $request->boolean('is_default');
        $validated['is_active'] = $request->has('is_active') ? $request->boolean('is_active') : true;

        // Parse variables in content
        preg_match_all('/\{([^}]+)\}/', $validated['content'], $matches);
        $validated['variables'] = array_unique($matches[1] ?? []);

        // If is_default is true, remove default status from other templates of same type
        if ($validated['is_default']) {
            ReminderTemplate::where('clinic_id', $whatsappTemplate->clinic_id)
                ->where('type', $validated['type'])
                ->where('channel', 'whatsapp')
                ->where('id', '!=', $whatsappTemplate->id)
                ->update(['is_default' => false]);
        }

        $old = ['name' => $whatsappTemplate->name, 'type' => $whatsappTemplate->type];
        $whatsappTemplate->update($validated);
        $this->auditLogService->logUpdated('ReminderTemplate', $whatsappTemplate->id, $old, ['name' => $whatsappTemplate->name, 'type' => $whatsappTemplate->type]);

        return redirect()->route('communication.whatsapp-templates.index')
            ->with('success', 'WhatsApp Template berhasil diperbarui.');
    }

    public function destroy(ReminderTemplate $whatsappTemplate)
    {
        abort_if($whatsappTemplate->clinic_id !== Auth::user()->clinic_id, 403);
        
        $whatsappTemplate->delete();
        $this->auditLogService->logDeleted('ReminderTemplate', $whatsappTemplate->id, ['name' => $whatsappTemplate->name]);

        return redirect()->route('communication.whatsapp-templates.index')
            ->with('success', 'WhatsApp Template berhasil dihapus.');
    }
}
