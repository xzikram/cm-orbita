<?php

namespace App\Modules\FollowUp\Controllers;

use App\Models\Event;
use App\Models\Patient;
use App\Core\Services\AuditLogService;
use App\Core\Services\PatientRegistrationService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class EventController extends Controller
{
    public function __construct(
        protected AuditLogService $auditLogService,
        protected PatientRegistrationService $registrationService
    ) {}

    // --- Admin Dashboard Methods ---

    public function index(Request $request)
    {
        $query = Event::where('clinic_id', Auth::user()->clinic_id);

        if ($search = $request->get('search')) {
            $query->where('name', 'LIKE', "%{$search}%")
                ->orWhere('location', 'LIKE', "%{$search}%");
        }

        $events = $query->withCount('patients')
            ->orderBy('event_date', 'desc')
            ->paginate(config('cfms.per_page', 10));

        return view('follow-up.events.index', compact('events'));
    }

    public function create()
    {
        return view('follow-up.events.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:100|unique:events,code',
            'event_date' => 'required|date',
            'location' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
        ]);

        $validated['clinic_id'] = Auth::user()->clinic_id;
        $validated['code'] = ($validated['code'] ?? null) ?: Str::slug($validated['name']) . '-' . rand(100, 999);
        $validated['is_active'] = true;

        $event = Event::create($validated);
        $this->auditLogService->logCreated('Event', $event->id, $validated);

        return redirect()->route('follow-up.events.show', $event)
            ->with('success', 'Event pemeriksaan mata gratis berhasil dibuat.');
    }

    public function show(Event $event)
    {
        abort_if($event->clinic_id !== Auth::user()->clinic_id, 403);

        $patients = $event->patients()
            ->orderBy('created_at', 'desc')
            ->paginate(config('cfms.per_page', 10));

        $arrivedCount = $event->patients()->whereNotNull('hospital_arrival_at')->count();

        // Generate QR code base64 completely offline on the server with JEC Blue
        $registerUrl = route('events.register', $event->code);
        $qrcodeBase64 = $this->generateQrCode($registerUrl);

        return view('follow-up.events.show', compact('event', 'patients', 'qrcodeBase64', 'arrivedCount'));
    }

    public function toggleActive(Event $event)
    {
        abort_if($event->clinic_id !== Auth::user()->clinic_id, 403);

        $event->update(['is_active' => !$event->is_active]);

        return redirect()->back()->with('success', 'Status event berhasil diperbarui.');
    }

    // --- Public Event Registration Methods ---

    public function registerForm($code)
    {
        $event = Event::where('code', $code)->firstOrFail();

        if (!$event->is_active) {
            return view('follow-up.events.inactive', compact('event'));
        }

        return view('follow-up.events.register', compact('event'));
    }

    public function registerSubmit(Request $request, $code)
    {
        $event = Event::where('code', $code)->firstOrFail();

        if (!$event->is_active) {
            abort(403, 'Event ini sudah tidak aktif.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'nik' => 'nullable|string|size:16',
            'phone' => 'required|string|max:20',
            'date_of_birth' => 'required|date',
            'gender' => 'required|in:L,P',
            'address' => 'nullable|string|max:500',
        ]);

        $validated['clinic_id'] = $event->clinic_id;
        $validated['registration_source'] = 'event';
        $validated['registration_source_id'] = $event->id;

        $patient = $this->registrationService->register($validated);

        return redirect()->route('events.ticket', ['code' => $event->code, 'patient' => $patient->id])
            ->with('success', 'Pendaftaran berhasil!');
    }

    public function ticket($code, Patient $patient)
    {
        $event = Event::where('code', $code)->firstOrFail();
        
        // Ensure patient belongs to this event
        if ($patient->registration_source !== 'event' || (int)$patient->registration_source_id !== (int)$event->id) {
            abort(404, 'Data pendaftaran tidak ditemukan.');
        }

        // Calculate queue number based on how many registered before this patient
        $queueNum = Patient::where('registration_source', 'event')
            ->where('registration_source_id', $event->id)
            ->where('id', '<=', $patient->id)
            ->count();

        $queueCode = 'EVT-' . str_pad($queueNum, 3, '0', STR_PAD_LEFT);

        // Generate QR code containing the MRN (medical record number) with JEC Blue
        $mrn = $patient->medical_record_number;
        $qrcodeBase64 = $this->generateQrCode($mrn);

        return view('follow-up.events.ticket', compact('event', 'patient', 'queueCode', 'qrcodeBase64'));
    }

    public function exportExcel(Event $event)
    {
        abort_if($event->clinic_id !== Auth::user()->clinic_id, 403);

        $patients = $event->patients()->orderBy('created_at', 'desc')->get();

        $headers = [
            'Content-Type' => 'application/vnd.ms-excel',
            'Content-Disposition' => 'attachment; filename="ekspor_event_' . Str::slug($event->name) . '_' . date('Ymd_His') . '.xls"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
        ];

        $xmlEscape = function($val) {
            return htmlspecialchars($val ?? '', ENT_XML1, 'UTF-8');
        };

        $callback = function() use ($event, $patients, $xmlEscape) {
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

            $xml .= ' <Worksheet ss:Name="' . substr($xmlEscape($event->name), 0, 30) . '">
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
            ->where('registration_source', 'event')
            ->with('event')
            ->orderBy('created_at', 'desc')
            ->get();

        $headers = [
            'Content-Type' => 'application/vnd.ms-excel',
            'Content-Disposition' => 'attachment; filename="ekspor_seluruh_event_' . date('Ymd_His') . '.xls"',
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

            $xml .= ' <Worksheet ss:Name="Semua Event">
  <Table>
   <Row ss:Height="20">
    <Cell ss:StyleID="Header"><Data ss:Type="String">Nama Event</Data></Cell>
    <Cell ss:StyleID="Header"><Data ss:Type="String">Lokasi Event</Data></Cell>
    <Cell ss:StyleID="Header"><Data ss:Type="String">Tanggal Event</Data></Cell>
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
                $eventName = $p->event ? $p->event->name : '-';
                $eventLocation = $p->event ? $p->event->location : '-';
                $eventDate = $p->event && $p->event->event_date ? $p->event->event_date->format('d/m/Y') : '-';

                $xml .= '   <Row>
    <Cell><Data ss:Type="String">' . $xmlEscape($eventName) . '</Data></Cell>
    <Cell><Data ss:Type="String">' . $xmlEscape($eventLocation) . '</Data></Cell>
    <Cell><Data ss:Type="String">' . $xmlEscape($eventDate) . '</Data></Cell>
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
        $options = new \chillerlan\QRCode\QROptions([
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

        return (new \chillerlan\QRCode\QRCode($options))->render($data);
    }
}
