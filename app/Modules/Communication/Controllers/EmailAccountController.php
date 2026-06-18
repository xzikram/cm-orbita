<?php

namespace App\Modules\Communication\Controllers;

use App\Core\Services\AuditLogService;
use App\Models\EmailAccount;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class EmailAccountController extends Controller
{
    public function __construct(protected AuditLogService $auditLogService) {}

    public function index()
    {
        $accounts = EmailAccount::where('clinic_id', Auth::user()->clinic_id)
            ->latest()
            ->paginate(config('cfms.per_page'));

        return view('communication.email-accounts.index', compact('accounts'));
    }

    public function create()
    {
        return view('communication.email-accounts.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email_address' => 'required|email|max:255',
            'smtp_host' => 'required|string|max:255',
            'smtp_port' => 'required|integer',
            'smtp_username' => 'required|string|max:255',
            'smtp_password' => 'required|string',
            'encryption' => 'nullable|string|in:tls,ssl,starttls',
            'is_default' => 'boolean',
            'is_active' => 'boolean',
        ]);

        $validated['clinic_id'] = Auth::user()->clinic_id;

        if ($request->boolean('is_default')) {
            EmailAccount::where('clinic_id', Auth::user()->clinic_id)->update(['is_default' => false]);
        }

        $account = EmailAccount::create($validated);
        $this->auditLogService->logCreated('EmailAccount', $account->id, ['name' => $account->name, 'email' => $account->email_address]);

        return redirect()->route('communication.email-accounts.index')
            ->with('success', 'Email Account created successfully.');
    }

    public function edit(EmailAccount $emailAccount)
    {
        abort_if($emailAccount->clinic_id !== Auth::user()->clinic_id, 403);
        return view('communication.email-accounts.edit', compact('emailAccount'));
    }

    public function update(Request $request, EmailAccount $emailAccount)
    {
        abort_if($emailAccount->clinic_id !== Auth::user()->clinic_id, 403);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email_address' => 'required|email|max:255',
            'smtp_host' => 'required|string|max:255',
            'smtp_port' => 'required|integer',
            'smtp_username' => 'required|string|max:255',
            'smtp_password' => 'nullable|string', // Optional on update
            'encryption' => 'nullable|string|in:tls,ssl,starttls',
            'is_default' => 'boolean',
            'is_active' => 'boolean',
        ]);

        if (empty($validated['smtp_password'])) {
            unset($validated['smtp_password']);
        }

        if ($request->boolean('is_default') && !$emailAccount->is_default) {
            EmailAccount::where('clinic_id', Auth::user()->clinic_id)->update(['is_default' => false]);
        }

        $old = ['name' => $emailAccount->name, 'email' => $emailAccount->email_address];
        $emailAccount->update($validated);
        $this->auditLogService->logUpdated('EmailAccount', $emailAccount->id, $old, ['name' => $emailAccount->name, 'email' => $emailAccount->email_address]);

        return redirect()->route('communication.email-accounts.index')
            ->with('success', 'Email Account updated successfully.');
    }

    public function destroy(EmailAccount $emailAccount)
    {
        abort_if($emailAccount->clinic_id !== Auth::user()->clinic_id, 403);
        $emailAccount->delete();
        $this->auditLogService->logDeleted('EmailAccount', $emailAccount->id, ['name' => $emailAccount->name]);

        return redirect()->route('communication.email-accounts.index')
            ->with('success', 'Email Account deleted successfully.');
    }
}
