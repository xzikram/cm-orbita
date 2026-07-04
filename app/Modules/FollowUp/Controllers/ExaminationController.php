<?php

namespace App\Modules\FollowUp\Controllers;

use App\Models\Doctor;
use App\Models\Examination;
use App\Models\Patient;
use App\Models\RefractionOptician;
use App\Modules\FollowUp\Services\FollowUpService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class ExaminationController extends Controller
{
    public function __construct(
        protected FollowUpService $followUpService,
        protected \App\Core\Services\PatientRegistrationService $registrationService
    ) {}

    public function index(Request $request)
    {
        $query = Examination::with(['patient', 'doctor', 'refractionOptician', 'creator'])
            ->where('clinic_id', Auth::user()->clinic_id);

        if ($search = $request->get('search')) {
            $query->whereHas('patient', fn($q) => $q->search($search));
        }

        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        if ($request->filled('downtime')) {
            $query->where('is_downtime_entry', $request->boolean('downtime'));
        }

        $examinations = $query->latest('examination_date')
            ->paginate(config('cfms.per_page'));

        return view('follow-up.examinations.index', compact('examinations'));
    }

    public function create(Request $request)
    {
        $clinicId = Auth::user()->clinic_id;
        $patients = Patient::where('clinic_id', $clinicId)->active()->orderBy('name')->get();
        $doctors = Doctor::where('clinic_id', $clinicId)->active()->orderBy('name')->get();
        $ros = RefractionOptician::where('clinic_id', $clinicId)->active()->orderBy('name')->get();

        $selectedPatient = $request->get('patient_id') ? Patient::find($request->get('patient_id')) : null;

        return view('follow-up.examinations.create', compact('patients', 'doctors', 'ros', 'selectedPatient'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'doctor_id' => 'required|exists:doctors,id',
            'ro_id' => 'nullable|exists:refraction_opticians,id',
            'examination_date' => 'required|date',
            'od_sphere' => 'nullable|numeric|between:-30,30',
            'od_cylinder' => 'nullable|numeric|between:-15,0',
            'od_axis' => 'nullable|integer|between:0,180',
            'od_visus' => 'nullable|string|max:20',
            'os_sphere' => 'nullable|numeric|between:-30,30',
            'os_cylinder' => 'nullable|numeric|between:-15,0',
            'os_axis' => 'nullable|integer|between:0,180',
            'os_visus' => 'nullable|string|max:20',
            'lens_type' => 'nullable|string|max:255',
            'lens_brand' => 'nullable|string|max:255',
            'lens_power_od' => 'nullable|string|max:20',
            'lens_power_os' => 'nullable|string|max:20',
            'clinical_notes' => 'nullable|string|max:2000',
            
            // Downtime & Transaction columns
            'patient_status' => 'nullable|in:Lama,Baru',
            'registration_date' => 'nullable|date',
            'registration_number' => 'nullable|string|max:255',
            'guarantor' => 'nullable|string|max:255',
            'service_unit' => 'nullable|string|max:255',
            'tindakan' => 'nullable|string|max:255',
            'queue_number' => 'nullable|string|max:255',
            'total_payment' => 'nullable|numeric|min:0',
        ]);

        $validated['clinic_id'] = Auth::user()->clinic_id;
        $validated['created_by'] = Auth::id();
        $validated['is_downtime_entry'] = $request->boolean('is_downtime_entry');
        if (!empty($validated['total_payment'])) {
            $validated['total_payment'] = (float) $validated['total_payment'];
        }

        $examination = $this->followUpService->createExamination($validated);

        return redirect()->route('follow-up.examinations.show', $examination)
            ->with('success', 'Pemeriksaan berhasil disimpan. Jadwal follow-up telah dibuat otomatis.');
    }

    public function createDowntime(Request $request)
    {
        $clinicId = Auth::user()->clinic_id;
        $patients = Patient::where('clinic_id', $clinicId)->where('is_active', true)->orderBy('name')->get();
        $doctors = \App\Models\Doctor::where('clinic_id', $clinicId)->where('is_active', true)->orderBy('name')->get();
        
        $ros = \App\Models\RefractionOptician::where('clinic_id', $clinicId)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $selectedPatient = null;
        if ($patientId = $request->get('patient_id')) {
            $selectedPatient = Patient::where('clinic_id', $clinicId)->find($patientId);
        }

        return view('follow-up.examinations.create-downtime', compact('patients', 'doctors', 'ros', 'selectedPatient'));
    }

    public function storeDowntime(Request $request)
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'doctor_id' => 'required|exists:doctors,id',
            'examination_date' => 'required|date',
            'registration_date' => 'required|date',
            'registration_number' => 'nullable|string|max:255',
            'guarantor' => 'required|string|max:255',
            'total_payment' => 'required|numeric|min:0',
        ]);

        $validated['clinic_id'] = Auth::user()->clinic_id;
        $validated['created_by'] = Auth::id();
        $validated['is_downtime_entry'] = true;
        $validated['total_payment'] = (float) $validated['total_payment'];
        
        // Sensible defaults for fields not in the simplified Excel form
        $ro = \App\Models\RefractionOptician::where('clinic_id', $validated['clinic_id'])->first();
        $validated['ro_id'] = $ro ? $ro->id : 1;
        $validated['patient_status'] = 'Lama';
        $validated['service_unit'] = 'EYE CLINIC';
        $validated['tindakan'] = 'Pemeriksaan';
        $validated['queue_number'] = '-';
        $validated['clinical_notes'] = 'Pencatatan manual transaksi saat downtime SIMRS.';

        $examination = $this->followUpService->createExamination($validated);

        return redirect()->route('follow-up.examinations.show', $examination)
            ->with('success', 'Transaksi downtime berhasil disimpan. Jadwal follow-up telah dibuat otomatis.');
    }

    public function show(Examination $examination)
    {
        abort_if($examination->clinic_id !== Auth::user()->clinic_id, 403);

        $examination->load([
            'patient', 'doctor', 'refractionOptician', 'creator',
            'followUpSchedules' => fn($q) => $q->with('latestVisit.followUpStatus')->orderBy('sequence'),
        ]);

        return view('follow-up.examinations.show', compact('examination'));
    }

    public function destroy(Request $request, Examination $examination)
    {
        abort_if($examination->clinic_id !== Auth::user()->clinic_id, 403);
        abort_if(!Auth::user()->hasRole('super-admin'), 403, 'Hanya Super Admin yang dapat menghapus data.');

        $request->validate([
            'reason' => 'required|string|max:1000',
        ]);

        $old = $examination->toArray();

        // 1. Soft delete related follow-up schedules & visits if any
        $examination->followUpSchedules()->delete();
        $examination->followUpVisits()->delete();

        // 2. Soft delete the examination
        $examination->delete();

        // Log using AuditLogService
        $auditLogService = resolve(\App\Core\Services\AuditLogService::class);
        $auditLogService->logDeleted('Examination', $examination->id, $old);

        $patient = $examination->patient()->withTrashed()->first();
        \App\Models\DeletionLog::create([
            'user_id' => Auth::id(),
            'model_type' => get_class($examination),
            'model_id' => $examination->id,
            'model_name' => 'Pemeriksaan ' . ($patient->name ?? '-') . ' (' . ($examination->examination_date?->format('Y-m-d') ?? '-') . ')',
            'model_identifier' => $examination->registration_number ?? '-',
            'reason' => $request->input('reason'),
        ]);

        return redirect()->route('follow-up.examinations.index')
            ->with('success', 'Data pemeriksaan berhasil dihapus.');
    }

    public function exportCsv(Request $request)
    {
        $query = Examination::with(['patient', 'doctor', 'refractionOptician'])
            ->where('clinic_id', Auth::user()->clinic_id);

        if ($search = $request->get('search')) {
            $query->whereHas('patient', fn($q) => $q->search($search));
        }

        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        if ($request->filled('downtime')) {
            $query->where('is_downtime_entry', $request->boolean('downtime'));
        } else {
            $query->where(function($q) {
                $q->where('is_downtime_entry', true)
                  ->orWhereHas('patient', function($pq) {
                      $pq->where('is_downtime_entry', true)
                        ->orWhere('medical_record_number', 'like', 'TEMP-%')
                        ->orWhereRaw("medical_record_number REGEXP '^[0-9]{8}-[0-9]{6}$'");
                  });
            });
        }

        $examinations = $query->latest('examination_date')->get();

        $belumUpdate = [];
        $sudahUpdate = [];

        foreach ($examinations as $exam) {
            $rm = $exam->patient->medical_record_number ?? '';
            $isTemp = str_starts_with($rm, 'TEMP-') || preg_match('/^\d{8}-\d{6}$/', $rm);
            if ($isTemp) {
                $belumUpdate[] = $exam;
            } else {
                $sudahUpdate[] = $exam;
            }
        }

        $headers = [
            'Content-Type' => 'application/vnd.ms-excel',
            'Content-Disposition' => 'attachment; filename="laporan_transaksi_downtime_' . date('Ymd_His') . '.xls"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
        ];

        $xmlEscape = function($val) {
            return htmlspecialchars($val ?? '', ENT_XML1, 'UTF-8');
        };

        $callback = function() use ($belumUpdate, $sudahUpdate, $xmlEscape) {
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
   <Interior ss:Color="#1E3A8A" ss:Pattern="Solid"/>
  </Style>
 </Styles>';

            // Sheet 1: Belum Update RM
            $xml .= ' <Worksheet ss:Name="Belum Update RM">
  <Table>
   <Row ss:Height="20">
    <Cell ss:StyleID="Header"><Data ss:Type="String">TANGGAL PEMERIKSAAN</Data></Cell>
    <Cell ss:StyleID="Header"><Data ss:Type="String">TANGGAL REGISTRASI</Data></Cell>
    <Cell ss:StyleID="Header"><Data ss:Type="String">NO. REGISTRASI</Data></Cell>
    <Cell ss:StyleID="Header"><Data ss:Type="String">NAMA DOKTER</Data></Cell>
    <Cell ss:StyleID="Header"><Data ss:Type="String">NAMA PASIEN</Data></Cell>
    <Cell ss:StyleID="Header"><Data ss:Type="String">NO. RM SEMENTARA</Data></Cell>
    <Cell ss:StyleID="Header"><Data ss:Type="String">GUARANTOR</Data></Cell>
    <Cell ss:StyleID="Header"><Data ss:Type="String">JUMLAH</Data></Cell>
   </Row>';
            foreach ($belumUpdate as $exam) {
                $xml .= '   <Row>
    <Cell><Data ss:Type="String">' . ($exam->examination_date ? $exam->examination_date->format('d/m/Y') : '') . '</Data></Cell>
    <Cell><Data ss:Type="String">' . ($exam->registration_date ? $exam->registration_date->format('d/m/Y') : '') . '</Data></Cell>
    <Cell><Data ss:Type="String">' . $xmlEscape($exam->registration_number) . '</Data></Cell>
    <Cell><Data ss:Type="String">' . $xmlEscape($exam->doctor->name ?? '') . '</Data></Cell>
    <Cell><Data ss:Type="String">' . $xmlEscape($exam->patient->name ?? '') . '</Data></Cell>
    <Cell><Data ss:Type="String">' . $xmlEscape($exam->patient->medical_record_number ?? '') . '</Data></Cell>
    <Cell><Data ss:Type="String">' . $xmlEscape($exam->guarantor) . '</Data></Cell>
    <Cell><Data ss:Type="String">Rp ' . ($exam->total_payment ? number_format($exam->total_payment, 0, ',', '.') : '0') . '</Data></Cell>
   </Row>';
            }
            $xml .= '  </Table>
 </Worksheet>';

            // Sheet 2: Sudah Update RM
            $xml .= ' <Worksheet ss:Name="Sudah Update RM">
  <Table>
   <Row ss:Height="20">
    <Cell ss:StyleID="Header"><Data ss:Type="String">TANGGAL PEMERIKSAAN</Data></Cell>
    <Cell ss:StyleID="Header"><Data ss:Type="String">TANGGAL REGISTRASI</Data></Cell>
    <Cell ss:StyleID="Header"><Data ss:Type="String">NO. REGISTRASI</Data></Cell>
    <Cell ss:StyleID="Header"><Data ss:Type="String">NAMA DOKTER</Data></Cell>
    <Cell ss:StyleID="Header"><Data ss:Type="String">NAMA PASIEN</Data></Cell>
    <Cell ss:StyleID="Header"><Data ss:Type="String">NO. RM SEMENTARA</Data></Cell>
    <Cell ss:StyleID="Header"><Data ss:Type="String">NO. RM RESMI</Data></Cell>
    <Cell ss:StyleID="Header"><Data ss:Type="String">GUARANTOR</Data></Cell>
    <Cell ss:StyleID="Header"><Data ss:Type="String">JUMLAH</Data></Cell>
   </Row>';
            foreach ($sudahUpdate as $exam) {
                $xml .= '   <Row>
    <Cell><Data ss:Type="String">' . ($exam->examination_date ? $exam->examination_date->format('d/m/Y') : '') . '</Data></Cell>
    <Cell><Data ss:Type="String">' . ($exam->registration_date ? $exam->registration_date->format('d/m/Y') : '') . '</Data></Cell>
    <Cell><Data ss:Type="String">' . $xmlEscape($exam->registration_number) . '</Data></Cell>
    <Cell><Data ss:Type="String">' . $xmlEscape($exam->doctor->name ?? '') . '</Data></Cell>
    <Cell><Data ss:Type="String">' . $xmlEscape($exam->patient->name ?? '') . '</Data></Cell>
    <Cell><Data ss:Type="String">' . $xmlEscape($exam->patient->temporary_medical_record_number ?? '-') . '</Data></Cell>
    <Cell><Data ss:Type="String">' . $xmlEscape($exam->patient->medical_record_number ?? '') . '</Data></Cell>
    <Cell><Data ss:Type="String">' . $xmlEscape($exam->guarantor) . '</Data></Cell>
    <Cell><Data ss:Type="String">Rp ' . ($exam->total_payment ? number_format($exam->total_payment, 0, ',', '.') : '0') . '</Data></Cell>
   </Row>';
            }
            $xml .= '  </Table>
 </Worksheet>';

            $xml .= '</Workbook>';
            echo $xml;
        };

        return response()->stream($callback, 200, $headers);
    }

    public function showImportForm()
    {
        return view('follow-up.examinations.import-mapping');
    }

    public function importMapping(Request $request)
    {
        $request->validate([
            'text' => 'required|string',
        ]);

        $lines = explode("\n", $request->input('text'));
        $clinicId = Auth::user()->clinic_id;
        $creatorId = Auth::user()->id;

        // Find a fallback active doctor of the clinic
        $fallbackDoctor = \App\Models\Doctor::where('clinic_id', $clinicId)->where('is_active', true)->first();
        if (!$fallbackDoctor) {
            return redirect()->back()->with('error', 'Klinik Anda belum memiliki Dokter yang aktif. Silakan tambahkan dokter terlebih dahulu.');
        }

        // Get all doctors in the clinic for matching
        $doctors = \App\Models\Doctor::where('clinic_id', $clinicId)->get();

        $importedCount = 0;
        $errors = [];

        \Illuminate\Support\Facades\DB::beginTransaction();
        try {
            foreach ($lines as $index => $line) {
                $line = trim($line);
                if (empty($line)) {
                    continue;
                }

                // Split columns by tabs, semicolons, double spaces, pipes, or commas
                $cols = preg_split('/[\t;|]+| {2,}/', $line);
                if (count($cols) < 5) {
                    // Try parsing with comma if it's a simple CSV line, but avoid splitting if there are commas in names
                    if (str_contains($line, ',')) {
                        $cols = str_getcsv($line, ',');
                    }
                }

                if (count($cols) < 5) {
                    $errors[] = "Baris " . ($index + 1) . ": Format tidak valid (minimal memerlukan kolom Tanggal, Nama Pasien, No. RM, dan Jumlah).";
                    continue;
                }

                // If headers exist, skip them
                $firstColLower = strtolower(trim($cols[0]));
                if (str_contains($firstColLower, 'tanggal') || str_contains($firstColLower, 'tgl') || str_contains($firstColLower, 'pemeriksaan')) {
                    continue;
                }

                // Map columns:
                // 0: Tanggal Pemeriksaan
                // 1: Tanggal Registrasi
                // 2: No. Registrasi
                // 3: Nama Dokter
                // 4: Nama Pasien
                // 5: Nomor Rekam Medik
                // 6: Guarantor
                // 7: Jumlah
                $examDateStr = trim($cols[0] ?? '');
                $regDateStr = trim($cols[1] ?? '');
                $regNo = trim($cols[2] ?? '');
                $doctorNameStr = trim($cols[3] ?? '');
                $patientName = trim($cols[4] ?? '');
                $mrNumber = trim($cols[5] ?? '');
                $guarantor = trim($cols[6] ?? '');
                $paymentStr = trim($cols[7] ?? '');

                if (empty($patientName)) {
                    $errors[] = "Baris " . ($index + 1) . ": Nama pasien tidak boleh kosong.";
                    continue;
                }

                // Parse dates
                $examDate = null;
                if (!empty($examDateStr)) {
                    try {
                        $examDate = \Carbon\Carbon::createFromFormat('d/m/Y', $examDateStr)->format('Y-m-d');
                    } catch (\Exception $e) {
                        try {
                            $examDate = \Carbon\Carbon::parse($examDateStr)->format('Y-m-d');
                        } catch (\Exception $ex) {
                            $examDate = now()->format('Y-m-d');
                        }
                    }
                } else {
                    $examDate = now()->format('Y-m-d');
                }

                $regDate = null;
                if (!empty($regDateStr)) {
                    try {
                        $regDate = \Carbon\Carbon::createFromFormat('d/m/Y', $regDateStr)->format('Y-m-d');
                    } catch (\Exception $e) {
                        try {
                            $regDate = \Carbon\Carbon::parse($regDateStr)->format('Y-m-d');
                        } catch (\Exception $ex) {
                            $regDate = $examDate;
                        }
                    }
                } else {
                    $regDate = $examDate;
                }

                // Parse payment amount
                $payment = 0;
                if (!empty($paymentStr)) {
                    $cleanAmt = preg_replace('/[^\d]/', '', $paymentStr);
                    $payment = (float) $cleanAmt;
                }

                // 1. Find or create patient using registration service
                $targetRm = (!empty($mrNumber) && !str_contains(strtolower($mrNumber), 'temp')) ? $mrNumber : null;
                $patient = $this->registrationService->register([
                    'clinic_id' => $clinicId,
                    'name' => $patientName,
                    'medical_record_number' => $targetRm,
                    'gender' => 'L', // fallback for import
                    'registration_source' => 'downtime'
                ]);

                // 2. Find matching doctor
                $doctor = null;
                if (!empty($doctorNameStr)) {
                    $doctorNameLower = strtolower($doctorNameStr);
                    foreach ($doctors as $doc) {
                        $docNameLower = strtolower($doc->name);
                        $cleanDocName = preg_replace('/^dr\.\s*|,.*$/', '', $docNameLower);
                        
                        $words = explode(' ', trim($cleanDocName));
                        $initials = '';
                        foreach ($words as $w) {
                            $initials .= substr($w, 0, 1);
                        }

                        if ($docNameLower === $doctorNameLower || $cleanDocName === $doctorNameLower || $initials === $doctorNameLower) {
                            $doctor = $doc;
                            break;
                        }
                    }

                    if (!$doctor) {
                        foreach ($doctors as $doc) {
                            if (str_contains(strtolower($doc->name), $doctorNameLower)) {
                                $doctor = $doc;
                                break;
                            }
                        }
                    }
                }

                if (!$doctor) {
                    $doctor = $fallbackDoctor;
                }

                $ro = \App\Models\RefractionOptician::where('clinic_id', $clinicId)->first();

                // 3. Create transaction (Examination)
                $examination = \App\Models\Examination::create([
                    'clinic_id' => $clinicId,
                    'patient_id' => $patient->id,
                    'doctor_id' => $doctor->id,
                    'ro_id' => $ro ? $ro->id : 1,
                    'created_by' => $creatorId,
                    'examination_date' => $examDate,
                    'registration_date' => $regDate,
                    'registration_number' => $regNo ?: ('REG/DWN/' . date('Ymd') . '/' . str_pad($index + 1, 4, '0', STR_PAD_LEFT)),
                    'guarantor' => $guarantor ?: 'PRIBADI',
                    'service_unit' => 'EYE CLINIC',
                    'total_payment' => $payment,
                    'is_downtime_entry' => true,
                    'status' => 'active',
                    'clinical_notes' => 'Diimpor otomatis dari transaksi manual Excel pasca-downtime.',
                ]);

                // Create follow up schedules automatically
                $followUpService = app(\App\Modules\FollowUp\Services\FollowUpService::class);
                $followUpService->generateSchedulesForExamination($examination);

                $importedCount++;
            }

            if (count($errors) > 0 && $importedCount === 0) {
                \Illuminate\Support\Facades\DB::rollBack();
                return redirect()->back()->withErrors($errors)->with('error', 'Gagal mengimpor transaksi. Silakan periksa data Anda.');
            }

            \Illuminate\Support\Facades\DB::commit();
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan sistem saat menyimpan data: ' . $e->getMessage());
        }

        $msg = "Berhasil mengimpor {$importedCount} data transaksi manual downtime.";
        if (count($errors) > 0) {
            return redirect()->route('follow-up.examinations.index')
                ->with('success', $msg)
                ->with('error_html', 'Beberapa baris dilewati:<br>' . implode('<br>', $errors));
        }

        return redirect()->route('follow-up.examinations.index')->with('success', $msg);
    }
}
