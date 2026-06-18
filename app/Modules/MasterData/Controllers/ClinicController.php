<?php

namespace App\Modules\MasterData\Controllers;

use App\Models\Clinic;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class ClinicController extends Controller
{
    public function index()
    {
        $clinics = Clinic::paginate(10);
        return view('master-data.clinics.index', compact('clinics'));
    }

    public function create()
    {
        return view('master-data.clinics.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'is_active' => 'boolean',
        ]);
        
        $validated['is_active'] = $request->has('is_active');
        Clinic::create($validated);
        return redirect()->route('master-data.clinics.index')->with('success', 'Cabang ditambahkan.');
    }

    public function edit(Clinic $clinic)
    {
        return view('master-data.clinics.edit', compact('clinic'));
    }

    public function update(Request $request, Clinic $clinic)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'is_active' => 'boolean',
        ]);
        
        $validated['is_active'] = $request->has('is_active');
        $clinic->update($validated);
        return redirect()->route('master-data.clinics.index')->with('success', 'Cabang diperbarui.');
    }
}
