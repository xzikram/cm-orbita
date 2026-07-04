<?php

namespace App\Modules\MasterData\Controllers;

use App\Core\Services\AuditLogService;
use App\Models\Doctor;
use App\Models\Clinic;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class DoctorController extends Controller
{
    public function __construct(
        protected AuditLogService $auditLogService
    ) {}

    public function index(Request $request)
    {
        $query = Doctor::with('clinic')
            ->where('clinic_id', Auth::user()->clinic_id);

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('sip_number', 'LIKE', "%{$search}%")
                  ->orWhere('specialization', 'LIKE', "%{$search}%");
            });
        }

        if ($request->has('status')) {
            $query->where('is_active', $request->boolean('status'));
        }

        $doctors = $query->orderBy('name')->paginate(config('cfms.per_page'));

        return view('master-data.doctors.index', compact('doctors'));
    }

    public function create()
    {
        return view('master-data.doctors.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'sip_number' => 'nullable|string|max:50',
            'specialization' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'is_active' => 'boolean',
        ]);

        $validated['clinic_id'] = Auth::user()->clinic_id;
        $validated['is_active'] = $request->boolean('is_active', true);

        $doctor = Doctor::create($validated);

        $this->auditLogService->logCreated('Doctor', $doctor->id, $validated);

        return redirect()->route('master-data.doctors.index')
            ->with('success', 'Dokter berhasil ditambahkan.');
    }

    public function edit(Doctor $doctor)
    {
        $this->authorizeClinic($doctor);
        return view('master-data.doctors.edit', compact('doctor'));
    }

    public function update(Request $request, Doctor $doctor)
    {
        $this->authorizeClinic($doctor);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'sip_number' => 'nullable|string|max:50',
            'specialization' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);
        $oldValues = $doctor->toArray();

        $doctor->update($validated);

        $this->auditLogService->logUpdated('Doctor', $doctor->id, $oldValues, $validated);

        return redirect()->route('master-data.doctors.index')
            ->with('success', 'Dokter berhasil diperbarui.');
    }

    public function destroy(Request $request, Doctor $doctor)
    {
        $this->authorizeClinic($doctor);
        abort_if(!Auth::user()->hasRole('super-admin'), 403, 'Hanya Super Admin yang dapat menghapus data.');

        $request->validate([
            'reason' => 'required|string|max:1000',
        ]);

        $oldValues = $doctor->toArray();

        $doctor->delete();

        $this->auditLogService->logDeleted('Doctor', $doctor->id, $oldValues);

        \App\Models\DeletionLog::create([
            'user_id' => Auth::id(),
            'model_type' => get_class($doctor),
            'model_id' => $doctor->id,
            'model_name' => $doctor->name,
            'model_identifier' => $doctor->initials ?? $doctor->sip_number ?? '-',
            'reason' => $request->input('reason'),
        ]);

        return redirect()->route('master-data.doctors.index')
            ->with('success', 'Dokter berhasil dihapus.');
    }

    public function showImportForm()
    {
        return view('master-data.doctors.import');
    }

    public function importMapping(Request $request)
    {
        $request->validate([
            'file' => 'nullable|file|mimes:csv,txt|max:2048',
            'text' => 'nullable|string',
        ]);

        if (!$request->hasFile('file') && !$request->filled('text')) {
            return redirect()->back()->with('error', 'Silakan pilih berkas CSV atau tempel teks dari Excel.');
        }

        $clinicId = Auth::user()->clinic_id;
        $importedCount = 0;
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
            if (str_contains($firstLine, "\t")) {
                $separator = "\t";
            }
            
            rewind($handle);
            
            $rowNum = 0;
            while (($row = fgetcsv($handle, 1000, $separator)) !== false) {
                $rowNum++;
                if (empty($row) || count($row) < 2) {
                    continue;
                }

                // Detect headers and skip
                $firstVal = strtolower(trim($row[0] ?? ''));
                $secondVal = strtolower(trim($row[1] ?? ''));
                if ($firstVal === 'no' || $firstVal === 'singkatan' || $secondVal === 'singkatan' || $secondVal === 'nama dokter') {
                    continue;
                }

                // If 3 columns (No, Singkatan, Nama Dokter)
                if (count($row) >= 3) {
                    $mappings[] = [
                        'row' => $rowNum,
                        'initials' => trim($row[1]),
                        'name' => trim($row[2])
                    ];
                } else {
                    // If 2 columns (Singkatan, Nama Dokter)
                    $mappings[] = [
                        'row' => $rowNum,
                        'initials' => trim($row[0]),
                        'name' => trim($row[1])
                    ];
                }
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
                $cols = preg_split('/[\t]+/', $line); // Prefer tabs for Excel copy-paste
                if (count($cols) < 2) {
                    $cols = preg_split('/[;,]+/', $line);
                }
                if (count($cols) < 2) {
                    $cols = preg_split('/\s{2,}/', $line); // 2 or more spaces
                }
                
                if (count($cols) >= 2) {
                    $firstVal = strtolower(trim($cols[0]));
                    $secondVal = strtolower(trim($cols[1]));
                    if ($firstVal === 'no' || $firstVal === 'singkatan' || $secondVal === 'singkatan' || $secondVal === 'nama dokter') {
                        continue;
                    }

                    if (count($cols) >= 3) {
                        $mappings[] = [
                            'row' => $rowNum,
                            'initials' => trim($cols[1]),
                            'name' => trim($cols[2])
                        ];
                    } else {
                        $mappings[] = [
                            'row' => $rowNum,
                            'initials' => trim($cols[0]),
                            'name' => trim($cols[1])
                        ];
                    }
                } else {
                    $errors[] = "Baris {$rowNum}: Format tidak valid. Pastikan data memiliki minimal 2 kolom (Singkatan & Nama Dokter).";
                }
            }
        }

        if (empty($mappings)) {
            return redirect()->back()->withErrors($errors)->with('error', 'Tidak ada data valid yang dapat diimpor.');
        }

        \Illuminate\Support\Facades\DB::beginTransaction();
        try {
            foreach ($mappings as $map) {
                $initials = $map['initials'];
                $name = $map['name'];
                $row = $map['row'];

                if (empty($initials) || empty($name)) {
                    continue;
                }

                // Extract specialization (e.g. Sp.M or Sp.M(K) or Sp.PD)
                $specialization = 'Spesialis Mata';
                if (preg_match('/(Sp\.[A-Za-z\(\)]+)/i', $name, $matches)) {
                    $specialization = trim($matches[1]);
                }

                // Check duplicate by initials or name in this clinic
                $existingDoctor = Doctor::where('clinic_id', $clinicId)
                    ->where(function($q) use ($initials, $name) {
                        $q->where('initials', $initials)
                          ->orWhere('name', $name);
                    })->first();

                if ($existingDoctor) {
                    $old = $existingDoctor->toArray();
                    $existingDoctor->update([
                        'name' => $name,
                        'initials' => $initials,
                        'specialization' => $specialization,
                    ]);
                    $this->auditLogService->logUpdated('Doctor', $existingDoctor->id, $old, $existingDoctor->toArray());
                    $updatedCount++;
                } else {
                    $doctor = Doctor::create([
                        'clinic_id' => $clinicId,
                        'name' => $name,
                        'initials' => $initials,
                        'specialization' => $specialization,
                        'is_active' => true,
                    ]);
                    $this->auditLogService->logCreated('Doctor', $doctor->id, $doctor->toArray());
                    $importedCount++;
                }
            }

            \Illuminate\Support\Facades\DB::commit();
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return redirect()->back()->with('error', 'Gagal memproses impor data: ' . $e->getMessage());
        }

        $msg = "Berhasil memproses data dokter: {$importedCount} ditambahkan, {$updatedCount} diperbarui.";
        if (count($errors) > 0) {
            return redirect()->route('master-data.doctors.index')
                ->with('success', $msg)
                ->with('error_html', 'Beberapa baris dilewati:<br>' . implode('<br>', $errors));
        }

        return redirect()->route('master-data.doctors.index')->with('success', $msg);
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

                $doctors = Doctor::withTrashed()->where('clinic_id', $clinicId)->get();
                $doctorIds = $doctors->pluck('id');

                if ($doctorIds->isNotEmpty()) {
                    // Hapus examinations terkait dokter
                    \App\Models\Examination::withTrashed()->whereIn('doctor_id', $doctorIds)->forceDelete();
                }

                // Hapus dokter dan user terkait
                foreach ($doctors as $doctor) {
                    $user = $doctor->user;
                    
                    $doctor->forceDelete();
                    
                    if ($user && $user->hasRole('dokter')) {
                        $user->delete();
                    }
                }

                // Re-enable FK checks
                \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=1');
            });

            $this->auditLogService->logDeleted('Doctor', null, ['description' => 'Menghapus seluruh master data dokter dan akun user dokter terkait.']);

            return redirect()->route('master-data.doctors.index')
                ->with('success', 'Semua data dokter berhasil dihapus.');
        } catch (\Exception $e) {
            // Re-enable FK checks in case of error
            \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=1');
            return redirect()->back()->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }

    protected function authorizeClinic(Doctor $doctor): void
    {
        abort_if($doctor->clinic_id !== Auth::user()->clinic_id, 403);
    }
}
