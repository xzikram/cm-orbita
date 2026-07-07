<?php

namespace App\Modules\FollowUp\Controllers;

use App\Core\Services\AuditLogService;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class PatientController extends Controller
{
    public function __construct(protected AuditLogService $auditLogService) {}

    public function index(Request $request)
    {
        $query = Patient::where('clinic_id', Auth::user()->clinic_id);

        if ($search = $request->get('search')) {
            $query->search($search);
        }

        if ($request->filled('downtime')) {
            $query->where('is_downtime_entry', $request->boolean('downtime'));
        }

        if ($request->filled('registration_source')) {
            $query->where('registration_source', $request->get('registration_source'));
        }

        $patients = $query->withCount('examinations')
            ->orderBy('name')
            ->paginate(config('cfms.per_page'));

        return view('follow-up.patients.index', compact('patients'));
    }

    public function create()
    {
        return view('follow-up.patients.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'medical_record_number' => [
                'required',
                'string',
                'max:50',
                Rule::unique('patients', 'medical_record_number')
                    ->where('clinic_id', Auth::user()->clinic_id)
            ],
            'name' => 'required|string|max:255',
            'nik' => 'nullable|string|max:16',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'gender' => 'nullable|in:L,P',
            'date_of_birth' => 'nullable|date',
            'address' => 'nullable|string|max:500',
            'notes' => 'nullable|string|max:1000',
            'parent_spouse_name' => 'nullable|string|max:255',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:20',
        ]);

        $validated['clinic_id'] = Auth::user()->clinic_id;
        $validated['is_downtime_entry'] = $request->boolean('is_downtime_entry');

        $patient = Patient::create($validated);
        $this->auditLogService->logCreated('Patient', $patient->id, $validated);

        return redirect()->route('follow-up.patients.index')
            ->with('success', 'Pasien berhasil ditambahkan.');
    }

    public function show(Patient $patient)
    {
        abort_if($patient->clinic_id !== Auth::user()->clinic_id, 403);

        $patient->load([
            'examinations' => fn($q) => $q->with(['doctor', 'refractionOptician'])->latest('examination_date'),
            'followUpSchedules' => fn($q) => $q->with('latestVisit')->orderBy('scheduled_date'),
            'documentDeliveries' => fn($q) => $q->with(['documentType', 'sender'])->latest('created_at'),
            'reminders' => fn($q) => $q->with('template')->latest('created_at'),
        ]);

        // Build a unified timeline collection
        $timeline = collect();

        foreach ($patient->examinations as $exam) {
            $timeline->push([
                'type' => 'examination',
                'date' => $exam->examination_date,
                'title' => 'Pemeriksaan Awal',
                'description' => 'Diperiksa oleh ' . ($exam->doctor->name ?? '-'),
                'icon' => 'stethoscope',
                'color' => 'blue',
            ]);
        }

        foreach ($patient->followUpSchedules as $schedule) {
            if ($schedule->status === 'completed' && $schedule->latestVisit) {
                $timeline->push([
                    'type' => 'visit',
                    'date' => $schedule->latestVisit->visit_date,
                    'title' => 'Kunjungan Kontrol (' . $schedule->label . ')',
                    'description' => 'Status: Hadir',
                    'icon' => 'calendar-check',
                    'color' => 'green',
                ]);
            }
        }

        foreach ($patient->documentDeliveries as $delivery) {
            $timeline->push([
                'type' => 'email',
                'date' => $delivery->created_at,
                'title' => 'Email Terkirim',
                'description' => 'Dokumen: ' . ($delivery->documentType->name ?? '-'),
                'icon' => 'envelope',
                'color' => 'indigo',
                'status' => $delivery->status,
            ]);
        }

        foreach ($patient->reminders as $reminder) {
            $timeline->push([
                'type' => 'whatsapp',
                'date' => $reminder->created_at,
                'title' => 'WhatsApp Reminder',
                'description' => 'Pesan pengingat kontrol',
                'icon' => 'chat-bubble',
                'color' => 'teal',
                'status' => $reminder->status,
            ]);
        }

        $timeline = $timeline->sortByDesc('date')->values();

        return view('follow-up.patients.show', compact('patient', 'timeline'));
    }

    public function edit(Patient $patient)
    {
        abort_if($patient->clinic_id !== Auth::user()->clinic_id, 403);
        return view('follow-up.patients.edit', compact('patient'));
    }

    public function update(Request $request, Patient $patient)
    {
        abort_if($patient->clinic_id !== Auth::user()->clinic_id, 403);

        $validated = $request->validate([
            'medical_record_number' => [
                'required',
                'string',
                'max:50',
                Rule::unique('patients', 'medical_record_number')
                    ->where('clinic_id', Auth::user()->clinic_id)
                    ->ignore($patient->id)
            ],
            'name' => 'required|string|max:255',
            'nik' => 'nullable|string|max:16',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'gender' => 'nullable|in:L,P',
            'date_of_birth' => 'nullable|date',
            'address' => 'nullable|string|max:500',
            'notes' => 'nullable|string|max:1000',
            'parent_spouse_name' => 'nullable|string|max:255',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:20',
        ]);

        $validated['is_downtime_entry'] = $request->boolean('is_downtime_entry');

        $old = $patient->toArray();
        $patient->update($validated);
        $this->auditLogService->logUpdated('Patient', $patient->id, $old, $validated);

        return redirect()->route('follow-up.patients.show', $patient)
            ->with('success', 'Data pasien berhasil diperbarui.');
    }

    public function destroy(Request $request, Patient $patient)
    {
        abort_if($patient->clinic_id !== Auth::user()->clinic_id, 403);
        abort_if(!Auth::user()->hasRole('super-admin'), 403, 'Hanya Super Admin yang dapat menghapus data.');

        $request->validate([
            'reason' => 'required|string|max:1000',
        ]);

        $old = $patient->toArray();
        $patient->delete();

        $this->auditLogService->logDeleted('Patient', $patient->id, $old);

        \App\Models\DeletionLog::create([
            'user_id' => Auth::id(),
            'model_type' => get_class($patient),
            'model_id' => $patient->id,
            'model_name' => $patient->name,
            'model_identifier' => $patient->medical_record_number,
            'reason' => $request->input('reason'),
        ]);

        return redirect()->route('follow-up.patients.index')
            ->with('success', 'Pasien berhasil dihapus.');
    }

    public function deleteAll(Request $request)
    {
        $request->validate([
            'confirm_password' => 'required|string',
        ]);

        if ($request->input('confirm_password') !== 'Ikr@21983') {
            return redirect()->back()->with('error', 'Password konfirmasi salah.');
        }

        $clinicId = Auth::user()->clinic_id;

        try {
            \Illuminate\Support\Facades\DB::transaction(function () use ($clinicId) {
                // Disable FK checks to avoid cascade conflicts
                \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=0');

                // 1. Hapus reminder logs terkait klinik ini
                $reminderIds = \App\Models\Reminder::withTrashed()->where('clinic_id', $clinicId)->pluck('id');
                if ($reminderIds->isNotEmpty()) {
                    \App\Models\ReminderLog::whereIn('reminder_id', $reminderIds)->delete();
                    \App\Models\Reminder::withTrashed()->where('clinic_id', $clinicId)->forceDelete();
                }

                // 2. Hapus processed documents terkait pasien di klinik ini
                $patientIds = \App\Models\Patient::withTrashed()->where('clinic_id', $clinicId)->pluck('id');
                if ($patientIds->isNotEmpty()) {
                    \App\Models\ProcessedDocument::withTrashed()->whereIn('patient_id', $patientIds)->forceDelete();
                    \App\Models\DocumentDelivery::whereIn('patient_id', $patientIds)->delete();

                    // 3. Hapus follow-up visits & schedules
                    \App\Models\FollowUpVisit::withTrashed()->whereIn('patient_id', $patientIds)->forceDelete();
                    \App\Models\FollowUpSchedule::withTrashed()->whereIn('patient_id', $patientIds)->forceDelete();

                    // 4. Hapus examinations
                    \App\Models\Examination::withTrashed()->whereIn('patient_id', $patientIds)->forceDelete();
                }

                // 5. Force delete patients
                \App\Models\Patient::withTrashed()->where('clinic_id', $clinicId)->forceDelete();

                // Re-enable FK checks
                \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=1');
            });

            $this->auditLogService->logDeleted('Patient', null, ['description' => 'Menghapus seluruh master data pasien dan data transaksi terkait.']);

            return redirect()->route('follow-up.patients.index')
                ->with('success', 'Semua data pasien dan transaksi terkait berhasil dihapus.');
        } catch (\Exception $e) {
            // Re-enable FK checks in case of error
            \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=1');
            return redirect()->back()->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }

    public function exportCsv(Request $request)
    {
        $query = Patient::where('clinic_id', Auth::user()->clinic_id);

        if ($search = $request->get('search')) {
            $query->search($search);
        }

        if ($request->filled('downtime')) {
            $query->where('is_downtime_entry', $request->boolean('downtime'));
        } else {
            $query->where(function($q) {
                $q->where('is_downtime_entry', true)
                  ->orWhere('medical_record_number', 'like', 'TEMP-%')
                  ->orWhereRaw("medical_record_number REGEXP '^[0-9]{8}-[0-9]{6}$'");
            });
        }

        $patients = $query->orderBy('name')->get();

        $belumUpdate = [];
        $sudahUpdate = [];

        foreach ($patients as $p) {
            $isTemp = str_starts_with($p->medical_record_number, 'TEMP-') || preg_match('/^\d{8}-\d{6}$/', $p->medical_record_number);
            if ($isTemp) {
                $belumUpdate[] = $p;
            } else {
                $sudahUpdate[] = $p;
            }
        }

        $headers = [
            'Content-Type' => 'application/vnd.ms-excel',
            'Content-Disposition' => 'attachment; filename="laporan_pasien_downtime_' . date('Ymd_His') . '.xls"',
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
    <Cell ss:StyleID="Header"><Data ss:Type="String">No. RM Sementara</Data></Cell>
    <Cell ss:StyleID="Header"><Data ss:Type="String">NIK</Data></Cell>
    <Cell ss:StyleID="Header"><Data ss:Type="String">Nama Pasien</Data></Cell>
    <Cell ss:StyleID="Header"><Data ss:Type="String">Jenis Kelamin</Data></Cell>
    <Cell ss:StyleID="Header"><Data ss:Type="String">Tanggal Lahir</Data></Cell>
    <Cell ss:StyleID="Header"><Data ss:Type="String">No. Telfon</Data></Cell>
    <Cell ss:StyleID="Header"><Data ss:Type="String">Alamat</Data></Cell>
    <Cell ss:StyleID="Header"><Data ss:Type="String">Nama Orangtua/Pasangan</Data></Cell>
    <Cell ss:StyleID="Header"><Data ss:Type="String">Emergency Contact</Data></Cell>
    <Cell ss:StyleID="Header"><Data ss:Type="String">No. Telfon Emergency Contact</Data></Cell>
   </Row>';
            foreach ($belumUpdate as $p) {
                $xml .= '   <Row>
    <Cell><Data ss:Type="String">' . $xmlEscape($p->medical_record_number) . '</Data></Cell>
    <Cell><Data ss:Type="String">' . $xmlEscape($p->nik) . '</Data></Cell>
    <Cell><Data ss:Type="String">' . $xmlEscape($p->name) . '</Data></Cell>
    <Cell><Data ss:Type="String">' . ($p->gender == 'L' ? 'Laki-laki' : ($p->gender == 'P' ? 'Perempuan' : '')) . '</Data></Cell>
    <Cell><Data ss:Type="String">' . ($p->date_of_birth ? $p->date_of_birth->format('d/m/Y') : '') . '</Data></Cell>
    <Cell><Data ss:Type="String">' . $xmlEscape($p->phone) . '</Data></Cell>
    <Cell><Data ss:Type="String">' . $xmlEscape($p->address) . '</Data></Cell>
    <Cell><Data ss:Type="String">' . $xmlEscape($p->parent_spouse_name) . '</Data></Cell>
    <Cell><Data ss:Type="String">' . $xmlEscape($p->emergency_contact_name) . '</Data></Cell>
    <Cell><Data ss:Type="String">' . $xmlEscape($p->emergency_contact_phone) . '</Data></Cell>
   </Row>';
            }
            $xml .= '  </Table>
 </Worksheet>';

            // Sheet 2: Sudah Update RM
            $xml .= ' <Worksheet ss:Name="Sudah Update RM">
  <Table>
   <Row ss:Height="20">
    <Cell ss:StyleID="Header"><Data ss:Type="String">No. RM Sementara</Data></Cell>
    <Cell ss:StyleID="Header"><Data ss:Type="String">No. RM Resmi</Data></Cell>
    <Cell ss:StyleID="Header"><Data ss:Type="String">NIK</Data></Cell>
    <Cell ss:StyleID="Header"><Data ss:Type="String">Nama Pasien</Data></Cell>
    <Cell ss:StyleID="Header"><Data ss:Type="String">Jenis Kelamin</Data></Cell>
    <Cell ss:StyleID="Header"><Data ss:Type="String">Tanggal Lahir</Data></Cell>
    <Cell ss:StyleID="Header"><Data ss:Type="String">No. Telfon</Data></Cell>
    <Cell ss:StyleID="Header"><Data ss:Type="String">Alamat</Data></Cell>
    <Cell ss:StyleID="Header"><Data ss:Type="String">Nama Orangtua/Pasangan</Data></Cell>
    <Cell ss:StyleID="Header"><Data ss:Type="String">Emergency Contact</Data></Cell>
    <Cell ss:StyleID="Header"><Data ss:Type="String">No. Telfon Emergency Contact</Data></Cell>
   </Row>';
            foreach ($sudahUpdate as $p) {
                $xml .= '   <Row>
    <Cell><Data ss:Type="String">' . $xmlEscape($p->temporary_medical_record_number ?? '-') . '</Data></Cell>
    <Cell><Data ss:Type="String">' . $xmlEscape($p->medical_record_number) . '</Data></Cell>
    <Cell><Data ss:Type="String">' . $xmlEscape($p->nik) . '</Data></Cell>
    <Cell><Data ss:Type="String">' . $xmlEscape($p->name) . '</Data></Cell>
    <Cell><Data ss:Type="String">' . ($p->gender == 'L' ? 'Laki-laki' : ($p->gender == 'P' ? 'Perempuan' : '')) . '</Data></Cell>
    <Cell><Data ss:Type="String">' . ($p->date_of_birth ? $p->date_of_birth->format('d/m/Y') : '') . '</Data></Cell>
    <Cell><Data ss:Type="String">' . $xmlEscape($p->phone) . '</Data></Cell>
    <Cell><Data ss:Type="String">' . $xmlEscape($p->address) . '</Data></Cell>
    <Cell><Data ss:Type="String">' . $xmlEscape($p->parent_spouse_name) . '</Data></Cell>
    <Cell><Data ss:Type="String">' . $xmlEscape($p->emergency_contact_name) . '</Data></Cell>
    <Cell><Data ss:Type="String">' . $xmlEscape($p->emergency_contact_phone) . '</Data></Cell>
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
        return view('follow-up.patients.import-mapping');
    }

    public function importNewMrMapping(Request $request)
    {
        $request->validate([
            'file' => 'nullable|file|mimes:csv,txt|max:2048',
            'text' => 'nullable|string',
        ]);

        if (!$request->hasFile('file') && !$request->filled('text')) {
            return redirect()->back()->with('error', 'Silakan pilih berkas CSV atau tempel teks dari Excel.');
        }

        $clinicId = Auth::user()->clinic_id;
        $updatedCount = 0;
        $errors = [];
        $mappings = [];

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $path = $file->getRealPath();
            
            $handle = fopen($path, 'r');
            if ($handle === false) {
                 return redirect()->back()->with('error', 'Gagal membuka file CSV.');
            }

            // Detect separator
            $firstLine = fgets($handle);
            $separator = ',';
            if (str_contains($firstLine, ';')) {
                $separator = ';';
            }
            
            rewind($handle);
            
            $header = fgetcsv($handle, 1000, $separator);
            if ($header && isset($header[0])) {
                $header[0] = preg_replace('/[\x{FEFF}\x{200B}]/u', '', $header[0]);
            }

            $patientIdIndex = 0;
            $newMrIndex = 1;
            
            if ($header) {
                foreach ($header as $index => $colName) {
                    $colNameLower = strtolower(trim($colName));
                    if (in_array($colNameLower, ['patientid', 'patient_id', 'rm_lama', 'no. rm sementara', 'no. rm (sementara/new)'])) {
                        $patientIdIndex = $index;
                    }
                    if (in_array($colNameLower, ['newmr', 'new_mr', 'rm_baru', 'no. rm baru'])) {
                        $newMrIndex = $index;
                    }
                }
            }

            $rowNum = 1;
            while (($row = fgetcsv($handle, 1000, $separator)) !== false) {
                $rowNum++;
                if (empty($row) || count($row) <= max($patientIdIndex, $newMrIndex)) {
                    continue;
                }
                $mappings[] = [
                    'row' => $rowNum,
                    'temp' => trim($row[$patientIdIndex]),
                    'new' => trim($row[$newMrIndex])
                ];
            }
            fclose($handle);
        } else {
            $lines = explode("\n", $request->input('text'));
            $rowNum = 0;
            foreach ($lines as $line) {
                $rowNum++;
                $line = trim($line);
                if (empty($line)) continue;
                
                // Try splitting by common delimiters
                $cols = preg_split('/[\t;,|]+/', $line);
                if (count($cols) < 2) {
                    // Try splitting by multiple spaces
                    $cols = preg_split('/\s{2,}/', $line);
                }
                
                if (count($cols) >= 2) {
                    $mappings[] = [
                        'row' => $rowNum,
                        'temp' => trim($cols[0]),
                        'new' => trim($cols[1])
                    ];
                } else {
                    $errors[] = "Baris {$rowNum}: Format tidak valid. Pastikan data memiliki minimal 2 kolom (RM Sementara & RM Baru).";
                }
            }
        }

        \Illuminate\Support\Facades\DB::beginTransaction();
        try {
            foreach ($mappings as $map) {
                $tempMr = $map['temp'];
                $newMr = $map['new'];
                $rowNum = $map['row'];

                if (empty($tempMr) || empty($newMr)) {
                    continue;
                }

                // Skip header if copy-pasted header text
                if (strtolower($tempMr) === 'patientid' || strtolower($tempMr) === 'patient_id' || strtolower($tempMr) === 'rm sementara' || strtolower($tempMr) === 'no. rm sementara') {
                    continue;
                }

                $patient = Patient::where('clinic_id', $clinicId)
                    ->where('medical_record_number', $tempMr)
                    ->first();

                if ($patient) {
                    $exists = Patient::where('clinic_id', $clinicId)
                        ->where('medical_record_number', $newMr)
                        ->where('id', '!=', $patient->id)
                        ->exists();

                    if ($exists) {
                        $errors[] = "Baris {$rowNum}: No. RM Baru '{$newMr}' sudah terdaftar untuk pasien lain.";
                        continue;
                    }

                    $oldMr = $patient->medical_record_number;
                    $patient->update([
                        'medical_record_number' => $newMr,
                        'temporary_medical_record_number' => $patient->temporary_medical_record_number ?: $oldMr,
                    ]);

                    $this->auditLogService->logUpdated('Patient', $patient->id, 
                        ['medical_record_number' => $oldMr], 
                        ['medical_record_number' => $newMr]
                    );

                    $updatedCount++;
                } else {
                    $errors[] = "Baris {$rowNum}: Pasien dengan No. RM Sementara '{$tempMr}' tidak ditemukan di klinik ini.";
                }
            }

            if (count($errors) > 0 && $updatedCount == 0) {
                \Illuminate\Support\Facades\DB::rollBack();
                return redirect()->back()->withErrors($errors)->with('error', 'Gagal memproses pemetaan. Silakan periksa detail kesalahan.');
            }

            \Illuminate\Support\Facades\DB::commit();
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }

        $msg = "Berhasil memperbarui {$updatedCount} Nomor RM Pasien.";
        if (count($errors) > 0) {
            return redirect()->route('follow-up.patients.index')
                ->with('success', $msg)
                ->with('error_html', 'Beberapa baris dilewati:<br>' . implode('<br>', $errors));
        }

        return redirect()->route('follow-up.patients.index')->with('success', $msg);
    }

    public function quickUpdateRm(Request $request, Patient $patient)
    {
        abort_if($patient->clinic_id !== Auth::user()->clinic_id, 403);

        $validated = $request->validate([
            'medical_record_number' => [
                'required',
                'string',
                'max:50',
                Rule::unique('patients', 'medical_record_number')
                    ->where('clinic_id', Auth::user()->clinic_id)
                    ->ignore($patient->id)
            ],
        ]);

        $oldMr = $patient->medical_record_number;
        $newMr = $validated['medical_record_number'];

        $patient->update([
            'medical_record_number' => $newMr,
            'temporary_medical_record_number' => $patient->temporary_medical_record_number ?: $oldMr,
        ]);

        $this->auditLogService->logUpdated('Patient', $patient->id, 
            ['medical_record_number' => $oldMr], 
            ['medical_record_number' => $newMr]
        );

        return redirect()->back()->with('success', "Nomor RM pasien '{$patient->name}' berhasil diperbarui menjadi '{$newMr}'.");
    }

    public function markFollowUp(Request $request, Patient $patient)
    {
        abort_if($patient->clinic_id !== Auth::user()->clinic_id, 403);

        $validated = $request->validate([
            'needs_follow_up' => 'required|boolean',
            'follow_up_notes' => 'nullable|string|max:1000',
        ]);

        $old = [
            'needs_follow_up' => $patient->needs_follow_up,
            'follow_up_notes' => $patient->follow_up_notes,
        ];

        $patient->update($validated);

        $this->auditLogService->logUpdated('Patient', $patient->id, $old, $validated);

        $statusMessage = $patient->needs_follow_up 
            ? "Pasien '{$patient->name}' berhasil ditandai perlu follow-up." 
            : "Tanda follow-up untuk pasien '{$patient->name}' berhasil dihapus.";

        return redirect()->back()->with('success', $statusMessage);
    }
}
