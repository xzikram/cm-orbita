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
    public function __construct(protected FollowUpService $followUpService) {}

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
        ]);

        $validated['clinic_id'] = Auth::user()->clinic_id;
        $validated['created_by'] = Auth::id();

        $examination = $this->followUpService->createExamination($validated);

        return redirect()->route('follow-up.examinations.show', $examination)
            ->with('success', 'Pemeriksaan berhasil disimpan. Jadwal follow-up telah dibuat otomatis.');
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
}
