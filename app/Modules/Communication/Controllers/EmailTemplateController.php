<?php

namespace App\Modules\Communication\Controllers;

use App\Core\Services\AuditLogService;
use App\Models\EmailTemplate;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class EmailTemplateController extends Controller
{
    public function __construct(protected AuditLogService $auditLogService) {}

    public function index()
    {
        $templates = EmailTemplate::where('clinic_id', Auth::user()->clinic_id)
            ->latest()
            ->paginate(config('cfms.per_page'));

        return view('communication.email-templates.index', compact('templates'));
    }

    public function create()
    {
        return view('communication.email-templates.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:email_templates,code',
            'name' => 'required|string|max:255',
            'subject_template' => 'required|string|max:255',
            'html_body' => 'required|string',
            'is_active' => 'boolean',
        ]);

        $validated['clinic_id'] = Auth::user()->clinic_id;
        // Basic variables defined, could be dynamic in the future
        $validated['variables'] = ['{{patient_name}}', '{{mrn}}', '{{clinic_name}}', '{{document_name}}'];

        $template = EmailTemplate::create($validated);
        $this->auditLogService->logCreated('EmailTemplate', $template->id, ['code' => $template->code, 'name' => $template->name]);

        return redirect()->route('communication.email-templates.index')
            ->with('success', 'Email Template created successfully.');
    }

    public function edit(EmailTemplate $emailTemplate)
    {
        abort_if($emailTemplate->clinic_id !== Auth::user()->clinic_id, 403);
        return view('communication.email-templates.edit', compact('emailTemplate'));
    }

    public function update(Request $request, EmailTemplate $emailTemplate)
    {
        abort_if($emailTemplate->clinic_id !== Auth::user()->clinic_id, 403);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'subject_template' => 'required|string|max:255',
            'html_body' => 'required|string',
            'is_active' => 'boolean',
        ]);

        $old = ['name' => $emailTemplate->name];
        $emailTemplate->update($validated);
        $this->auditLogService->logUpdated('EmailTemplate', $emailTemplate->id, $old, ['name' => $emailTemplate->name]);

        return redirect()->route('communication.email-templates.index')
            ->with('success', 'Email Template updated successfully.');
    }

    public function destroy(EmailTemplate $emailTemplate)
    {
        abort_if($emailTemplate->clinic_id !== Auth::user()->clinic_id, 403);
        $emailTemplate->delete();
        $this->auditLogService->logDeleted('EmailTemplate', $emailTemplate->id, ['name' => $emailTemplate->name]);

        return redirect()->route('communication.email-templates.index')
            ->with('success', 'Email Template deleted successfully.');
    }

    public function deleteAll()
    {
        $clinicId = Auth::user()->clinic_id;
        $templates = EmailTemplate::where('clinic_id', $clinicId)->get();
        
        foreach ($templates as $template) {
            $template->delete();
            $this->auditLogService->logDeleted('EmailTemplate', $template->id, ['name' => $template->name]);
        }

        return redirect()->route('communication.email-templates.index')
            ->with('success', 'All email templates deleted successfully.');
    }
}
