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
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;

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

        $arrivedCount = $campaign->patients()->whereNotNull('hospital_arrival_at')->count();

        // Get clicks over time for visual tracking (grouped by date)
        $clicksOverTime = CampaignClick::where('campaign_id', $campaign->id)
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

        return view('follow-up.campaigns.show', compact('campaign', 'patients', 'clicksOverTime', 'arrivedCount'));
    }

    public function toggleActive(MarketingCampaign $campaign)
    {
        abort_if($campaign->clinic_id !== Auth::user()->clinic_id, 403);

        $campaign->update(['is_active' => !$campaign->is_active]);

        return redirect()->back()->with('success', 'Status promo link berhasil diperbarui.');
    }

    public function edit(MarketingCampaign $campaign)
    {
        abort_if($campaign->clinic_id !== Auth::user()->clinic_id, 403);
        return view('follow-up.campaigns.edit', compact('campaign'));
    }

    public function update(Request $request, MarketingCampaign $campaign)
    {
        abort_if($campaign->clinic_id !== Auth::user()->clinic_id, 403);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:100|unique:marketing_campaigns,code,' . $campaign->id,
            'source' => 'required|string|max:50',
            'landing_page_type' => 'required|string|in:direct,landing',
            'description' => 'nullable|string|max:2000',
            'video_url' => 'nullable|string|max:255',
            'brochure' => 'nullable|image|max:5120', // max 5MB
            'benefits' => 'nullable|array',
            'testimonials' => 'nullable|array',
        ]);

        if ($request->hasFile('brochure')) {
            $path = $request->file('brochure')->store('campaigns/brochures', 'public');
            $validated['brochure_image_path'] = $path;
        }

        // Clean up benefits & testimonials arrays to remove empty items
        if (isset($validated['benefits'])) {
            $validated['benefits'] = array_values(array_filter($validated['benefits']));
        }
        
        if (isset($validated['testimonials'])) {
            $validated['testimonials'] = array_values(array_filter($validated['testimonials'], function($t) {
                return !empty($t['name']) && !empty($t['text']);
            }));
        }

        $oldValues = $campaign->toArray();
        $campaign->update($validated);
        $this->auditLogService->logUpdated('MarketingCampaign', $campaign->id, $oldValues, $validated);

        return redirect()->route('follow-up.campaigns.show', $campaign)
            ->with('success', 'Link promosi marketing berhasil diperbarui.');
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

        // Generate QR code containing the MRN (medical record number)
        $mrn = $patient->medical_record_number;
        $qrcodeBase64 = $this->generateQrCode($mrn);

        return view('follow-up.campaigns.success', compact('campaign', 'patient', 'qrcodeBase64'));
    }

    public function exportExcel(MarketingCampaign $campaign)
    {
        abort_if($campaign->clinic_id !== Auth::user()->clinic_id, 403);

        $patients = $campaign->patients()->orderBy('created_at', 'desc')->get();

        $headers = [
            'Content-Type' => 'application/vnd.ms-excel',
            'Content-Disposition' => 'attachment; filename="ekspor_promo_' . Str::slug($campaign->name) . '_' . date('Ymd_His') . '.xls"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
        ];

        $xmlEscape = function($val) {
            return htmlspecialchars($val ?? '', ENT_XML1, 'UTF-8');
        };

        $callback = function() use ($campaign, $patients, $xmlEscape) {
            $xml = '<?xml version="1.0"?>
<?mso-application progid="Excel.Sheet"?>
<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet"
 xmlns:o="urn:schemas-microsoft-com:office:office"
 xmlns:x="urn:schemas-microsoft-com:office:excel"
 xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet"
 xmlns:html="http://www.w3.org/TR/REC-html40">
 <DocumentProperties xmlns="urn:schemas-microsoft-com:office:office">
  <Author>CFMS</Author>
  <LastAuthor>CFMS</LastAuthor>
  <Created>' . date('Y-m-d\TH:i:s\Z') . '</Created>
  <Version>16.00</Version>
 </DocumentProperties>
 <Styles>
  <Style ss:ID="Default" ss:Name="Normal">
   <Alignment ss:Vertical="Bottom"/>
   <Borders/>
   <Font ss:FontName="Calibri" x:CharSet="1" x:Family="Swiss" ss:Size="11" ss:Color="#000000"/>
   <Interior/>
   <NumberFormat/>
   <Protection/>
  </Style>
  <Style ss:ID="Header">
   <Font ss:FontName="Calibri" x:CharSet="1" x:Family="Swiss" ss:Size="11" ss:Color="#FFFFFF" ss:Bold="1"/>
   <Interior ss:Color="#1B4E80" ss:Pattern="Solid"/>
  </Style>
 </Styles>';

            $xml .= ' <Worksheet ss:Name="' . substr($xmlEscape($campaign->name), 0, 30) . '">
  <Table>
   <Row ss:Height="20">
    <Cell ss:StyleID="Header"><Data ss:Type="String">Nama Pasien</Data></Cell>
    <Cell ss:StyleID="Header"><Data ss:Type="String">No. RM Sementara</Data></Cell>
    <Cell ss:StyleID="Header"><Data ss:Type="String">NIK</Data></Cell>
    <Cell ss:StyleID="Header"><Data ss:Type="String">No. WhatsApp</Data></Cell>
    <Cell ss:StyleID="Header"><Data ss:Type="String">Jenis Kelamin</Data></Cell>
    <Cell ss:StyleID="Header"><Data ss:Type="String">Tanggal Lahir</Data></Cell>
    <Cell ss:StyleID="Header"><Data ss:Type="String">Umur</Data></Cell>
    <Cell ss:StyleID="Header"><Data ss:Type="String">Alamat</Data></Cell>
    <Cell ss:StyleID="Header"><Data ss:Type="String">Tanggal Daftar</Data></Cell>
    <Cell ss:StyleID="Header"><Data ss:Type="String">Status Kehadiran</Data></Cell>
    <Cell ss:StyleID="Header"><Data ss:Type="String">Waktu Check-in</Data></Cell>
   </Row>';

            foreach ($patients as $p) {
                $xml .= '   <Row>
    <Cell><Data ss:Type="String">' . $xmlEscape($p->name) . '</Data></Cell>
    <Cell><Data ss:Type="String">' . $xmlEscape($p->medical_record_number) . '</Data></Cell>
    <Cell><Data ss:Type="String">' . $xmlEscape($p->nik) . '</Data></Cell>
    <Cell><Data ss:Type="String">' . $xmlEscape($p->phone) . '</Data></Cell>
    <Cell><Data ss:Type="String">' . ($p->gender == 'L' ? 'Laki-laki' : ($p->gender == 'P' ? 'Perempuan' : $p->gender)) . '</Data></Cell>
    <Cell><Data ss:Type="String">' . ($p->date_of_birth ? $p->date_of_birth->format('d/m/Y') : '') . '</Data></Cell>
    <Cell><Data ss:Type="String">' . ($p->age ? $p->age . ' Tahun' : '-') . '</Data></Cell>
    <Cell><Data ss:Type="String">' . $xmlEscape($p->address) . '</Data></Cell>
    <Cell><Data ss:Type="String">' . ($p->created_at ? $p->created_at->format('d/m/Y H:i') : '') . '</Data></Cell>
    <Cell><Data ss:Type="String">' . ($p->hospital_arrival_at ? 'Hadir' : 'Belum Hadir') . '</Data></Cell>
    <Cell><Data ss:Type="String">' . ($p->hospital_arrival_at ? $p->hospital_arrival_at->timezone(config('app.timezone', 'Asia/Makassar'))->format('d/m/Y H:i') . ' WITA' : '-') . '</Data></Cell>
   </Row>';
            }

            $xml .= '  </Table>
 </Worksheet>';
            $xml .= '</Workbook>';
            echo $xml;
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportAllExcel()
    {
        $clinicId = Auth::user()->clinic_id;

        $patients = Patient::where('clinic_id', $clinicId)
            ->where('registration_source', 'marketing')
            ->with('campaign')
            ->orderBy('created_at', 'desc')
            ->get();

        $headers = [
            'Content-Type' => 'application/vnd.ms-excel',
            'Content-Disposition' => 'attachment; filename="ekspor_seluruh_promo_' . date('Ymd_His') . '.xls"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
        ];

        $xmlEscape = function($val) {
            return htmlspecialchars($val ?? '', ENT_XML1, 'UTF-8');
        };

        $callback = function() use ($patients, $xmlEscape) {
            $xml = '<?xml version="1.0"?>
<?mso-application progid="Excel.Sheet"?>
<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet"
 xmlns:o="urn:schemas-microsoft-com:office:office"
 xmlns:x="urn:schemas-microsoft-com:office:excel"
 xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet"
 xmlns:html="http://www.w3.org/TR/REC-html40">
 <DocumentProperties xmlns="urn:schemas-microsoft-com:office:office">
  <Author>CFMS</Author>
  <LastAuthor>CFMS</LastAuthor>
  <Created>' . date('Y-m-d\TH:i:s\Z') . '</Created>
  <Version>16.00</Version>
 </DocumentProperties>
 <Styles>
  <Style ss:ID="Default" ss:Name="Normal">
   <Alignment ss:Vertical="Bottom"/>
   <Borders/>
   <Font ss:FontName="Calibri" x:CharSet="1" x:Family="Swiss" ss:Size="11" ss:Color="#000000"/>
   <Interior/>
   <NumberFormat/>
   <Protection/>
  </Style>
  <Style ss:ID="Header">
   <Font ss:FontName="Calibri" x:CharSet="1" x:Family="Swiss" ss:Size="11" ss:Color="#FFFFFF" ss:Bold="1"/>
   <Interior ss:Color="#1B4E80" ss:Pattern="Solid"/>
  </Style>
 </Styles>';

            $xml .= ' <Worksheet ss:Name="Semua Promo">
  <Table>
   <Row ss:Height="20">
    <Cell ss:StyleID="Header"><Data ss:Type="String">Nama Promo</Data></Cell>
    <Cell ss:StyleID="Header"><Data ss:Type="String">Sumber Media</Data></Cell>
    <Cell ss:StyleID="Header"><Data ss:Type="String">Nama Pasien</Data></Cell>
    <Cell ss:StyleID="Header"><Data ss:Type="String">No. RM Sementara</Data></Cell>
    <Cell ss:StyleID="Header"><Data ss:Type="String">NIK</Data></Cell>
    <Cell ss:StyleID="Header"><Data ss:Type="String">No. WhatsApp</Data></Cell>
    <Cell ss:StyleID="Header"><Data ss:Type="String">Jenis Kelamin</Data></Cell>
    <Cell ss:StyleID="Header"><Data ss:Type="String">Tanggal Lahir</Data></Cell>
    <Cell ss:StyleID="Header"><Data ss:Type="String">Umur</Data></Cell>
    <Cell ss:StyleID="Header"><Data ss:Type="String">Alamat</Data></Cell>
    <Cell ss:StyleID="Header"><Data ss:Type="String">Tanggal Daftar</Data></Cell>
    <Cell ss:StyleID="Header"><Data ss:Type="String">Status Kehadiran</Data></Cell>
    <Cell ss:StyleID="Header"><Data ss:Type="String">Waktu Check-in</Data></Cell>
   </Row>';

            foreach ($patients as $p) {
                $campaignName = $p->campaign ? $p->campaign->name : '-';
                $campaignSource = $p->campaign ? $p->campaign->source : '-';

                $xml .= '   <Row>
    <Cell><Data ss:Type="String">' . $xmlEscape($campaignName) . '</Data></Cell>
    <Cell><Data ss:Type="String">' . $xmlEscape($campaignSource) . '</Data></Cell>
    <Cell><Data ss:Type="String">' . $xmlEscape($p->name) . '</Data></Cell>
    <Cell><Data ss:Type="String">' . $xmlEscape($p->medical_record_number) . '</Data></Cell>
    <Cell><Data ss:Type="String">' . $xmlEscape($p->nik) . '</Data></Cell>
    <Cell><Data ss:Type="String">' . $xmlEscape($p->phone) . '</Data></Cell>
    <Cell><Data ss:Type="String">' . ($p->gender == 'L' ? 'Laki-laki' : ($p->gender == 'P' ? 'Perempuan' : $p->gender)) . '</Data></Cell>
    <Cell><Data ss:Type="String">' . ($p->date_of_birth ? $p->date_of_birth->format('d/m/Y') : '') . '</Data></Cell>
    <Cell><Data ss:Type="String">' . ($p->age ? $p->age . ' Tahun' : '-') . '</Data></Cell>
    <Cell><Data ss:Type="String">' . $xmlEscape($p->address) . '</Data></Cell>
    <Cell><Data ss:Type="String">' . ($p->created_at ? $p->created_at->format('d/m/Y H:i') : '') . '</Data></Cell>
    <Cell><Data ss:Type="String">' . ($p->hospital_arrival_at ? 'Hadir' : 'Belum Hadir') . '</Data></Cell>
    <Cell><Data ss:Type="String">' . ($p->hospital_arrival_at ? $p->hospital_arrival_at->timezone(config('app.timezone', 'Asia/Makassar'))->format('d/m/Y H:i') . ' WITA' : '-') . '</Data></Cell>
   </Row>';
            }

            $xml .= '  </Table>
 </Worksheet>';
            $xml .= '</Workbook>';
            echo $xml;
        };

        return response()->stream($callback, 200, $headers);
    }

    private function generateQrCode($data)
    {
        $options = new QROptions([
            'eccLevel' => \chillerlan\QRCode\Common\EccLevel::H,
            'outputInterface' => \chillerlan\QRCode\Output\QRMarkupSVG::class,
            'outputBase64' => true,
            'moduleValues' => [
                \chillerlan\QRCode\Data\QRMatrix::M_DATA_DARK => '#1b4e80',
                \chillerlan\QRCode\Data\QRMatrix::M_FINDER_DARK => '#1b4e80',
                \chillerlan\QRCode\Data\QRMatrix::M_FINDER_DOT => '#1b4e80',
                \chillerlan\QRCode\Data\QRMatrix::M_ALIGNMENT_DARK => '#1b4e80',
                \chillerlan\QRCode\Data\QRMatrix::M_TIMING_DARK => '#1b4e80',
                \chillerlan\QRCode\Data\QRMatrix::M_FORMAT_DARK => '#1b4e80',
                \chillerlan\QRCode\Data\QRMatrix::M_VERSION_DARK => '#1b4e80',
                \chillerlan\QRCode\Data\QRMatrix::M_DARKMODULE => '#1b4e80',
                \chillerlan\QRCode\Data\QRMatrix::M_DATA => '#ffffff',
                \chillerlan\QRCode\Data\QRMatrix::M_FINDER => '#ffffff',
                \chillerlan\QRCode\Data\QRMatrix::M_ALIGNMENT => '#ffffff',
                \chillerlan\QRCode\Data\QRMatrix::M_TIMING => '#ffffff',
                \chillerlan\QRCode\Data\QRMatrix::M_FORMAT => '#ffffff',
                \chillerlan\QRCode\Data\QRMatrix::M_VERSION => '#ffffff',
                \chillerlan\QRCode\Data\QRMatrix::M_QUIETZONE => '#ffffff',
                \chillerlan\QRCode\Data\QRMatrix::M_SEPARATOR => '#ffffff',
            ],
        ]);

        return (new QRCode($options))->render($data);
    }
}
