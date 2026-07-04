<?php

namespace App\Modules\FollowUp\Controllers;

use App\Models\MarketingCampaign;
use App\Models\CampaignClick;
use App\Models\Patient;
use App\Core\Services\AuditLogService;
use App\Core\Services\PatientRegistrationService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CampaignController extends Controller
{
    public function __construct(
        protected AuditLogService $auditLogService,
        protected PatientRegistrationService $registrationService
    ) {}

    // --- Admin Dashboard Methods ---

    public function index(Request $request)
    {
        $query = MarketingCampaign::where('clinic_id', Auth::user()->clinic_id);

        if ($search = $request->get('search')) {
            $query->where('name', 'LIKE', "%{$search}%")
                ->orWhere('source', 'LIKE', "%{$search}%");
        }

        $campaigns = $query->withCount('patients')
            ->orderBy('created_at', 'desc')
            ->paginate(config('cfms.per_page', 10));

        return view('follow-up.campaigns.index', compact('campaigns'));
    }

    public function create()
    {
        return view('follow-up.campaigns.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:100|unique:marketing_campaigns,code',
            'source' => 'required|string|max:50',
        ]);

        $validated['clinic_id'] = Auth::user()->clinic_id;
        $validated['code'] = ($validated['code'] ?? null) ?: Str::slug($validated['name']) . '-' . rand(100, 999);
        $validated['is_active'] = true;
        $validated['clicks_count'] = 0;
        $validated['conversions_count'] = 0;

        $campaign = MarketingCampaign::create($validated);
        $this->auditLogService->logCreated('MarketingCampaign', $campaign->id, $validated);

        return redirect()->route('follow-up.campaigns.show', $campaign)
            ->with('success', 'Link promosi marketing berhasil dibuat.');
    }

    public function show(MarketingCampaign $campaign)
    {
        abort_if($campaign->clinic_id !== Auth::user()->clinic_id, 403);

        $patients = $campaign->patients()
            ->orderBy('created_at', 'desc')
            ->paginate(config('cfms.per_page', 10));

        // Get clicks over time for visual tracking (grouped by date)
        $clicksOverTime = CampaignClick::where('campaign_id', $campaign->id)
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

        return view('follow-up.campaigns.show', compact('campaign', 'patients', 'clicksOverTime'));
    }

    public function toggleActive(MarketingCampaign $campaign)
    {
        abort_if($campaign->clinic_id !== Auth::user()->clinic_id, 403);

        $campaign->update(['is_active' => !$campaign->is_active]);

        return redirect()->back()->with('success', 'Status promo link berhasil diperbarui.');
    }

    // --- Public Promo Tracking & Registration Methods ---

    public function trackAndRedirect(Request $request, $code)
    {
        $campaign = MarketingCampaign::where('code', $code)->firstOrFail();

        if (!$campaign->is_active) {
            return view('follow-up.campaigns.inactive', compact('campaign'));
        }

        // 1. Log click details
        CampaignClick::create([
            'campaign_id' => $campaign->id,
            'ip_address' => $request->ip(),
            'user_agent' => $request->header('User-Agent'),
            'referrer' => $request->header('referer'),
        ]);

        // 2. Increment click count
        $campaign->increment('clicks_count');

        return view('follow-up.campaigns.landing', compact('campaign'));
    }

    public function registerSubmit(Request $request, $code)
    {
        $campaign = MarketingCampaign::where('code', $code)->firstOrFail();

        if (!$campaign->is_active) {
            abort(403, 'Promo link ini sudah tidak aktif.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'nik' => 'nullable|string|size:16',
            'phone' => 'required|string|max:20',
            'date_of_birth' => 'required|date',
            'gender' => 'required|in:L,P',
            'address' => 'nullable|string|max:500',
        ]);

        $validated['clinic_id'] = $campaign->clinic_id;
        $validated['registration_source'] = 'marketing';
        $validated['registration_source_id'] = $campaign->id;

        // Register patient
        $patient = $this->registrationService->register($validated);

        // Increment conversion count
        $campaign->increment('conversions_count');

        return redirect()->route('campaign.success', ['code' => $campaign->code, 'patient' => $patient->id])
            ->with('success', 'Pendaftaran promo berhasil!');
    }

    public function success($code, Patient $patient)
    {
        $campaign = MarketingCampaign::where('code', $code)->firstOrFail();

        if ($patient->registration_source !== 'marketing' || (int)$patient->registration_source_id !== (int)$campaign->id) {
            abort(404, 'Data pendaftaran tidak ditemukan.');
        }

        return view('follow-up.campaigns.success', compact('campaign', 'patient'));
    }
}
