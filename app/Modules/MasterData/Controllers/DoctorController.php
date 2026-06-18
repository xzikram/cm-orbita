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

    public function destroy(Doctor $doctor)
    {
        $this->authorizeClinic($doctor);
        $oldValues = $doctor->toArray();

        $doctor->delete();

        $this->auditLogService->logDeleted('Doctor', $doctor->id, $oldValues);

        return redirect()->route('master.doctors.index')
            ->with('success', 'Dokter berhasil dihapus.');
    }

    protected function authorizeClinic(Doctor $doctor): void
    {
        abort_if($doctor->clinic_id !== Auth::user()->clinic_id, 403);
    }
}
