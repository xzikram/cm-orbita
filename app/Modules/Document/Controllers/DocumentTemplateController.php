<?php

namespace App\Modules\Document\Controllers;

use App\Models\DocumentTemplate;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class DocumentTemplateController extends Controller
{
    public function index()
    {
        $templates = DocumentTemplate::where('clinic_id', Auth::user()->clinic_id)
            ->latest()
            ->paginate(config('cfms.per_page'));

        return view('document.templates.index', compact('templates'));
    }

    public function create()
    {
        return view('document.templates.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'margin_top' => 'required|integer|min:0',
            'margin_bottom' => 'required|integer|min:0',
            'margin_left' => 'required|integer|min:0',
            'margin_right' => 'required|integer|min:0',
            'disclaimer_text' => 'nullable|string',
            'watermark_text' => 'nullable|string',
            'header_image' => 'nullable|image|max:2048',
            'footer_image' => 'nullable|image|max:2048',
            'is_active' => 'boolean',
        ]);

        $validated['clinic_id'] = Auth::user()->clinic_id;
        $validated['code'] = 'TPL-' . time() . '-' . strtoupper(\Illuminate\Support\Str::random(4));
        $validated['cover_design_type'] = 'standard';
        $validated['is_active'] = $request->boolean('is_active', true);

        if ($request->hasFile('header_image')) {
            $validated['header_logo_path'] = $request->file('header_image')->store('templates/logos', 'public');
        }
        if ($request->hasFile('footer_image')) {
            $validated['footer_logo_path'] = $request->file('footer_image')->store('templates/logos', 'public');
        }

        DocumentTemplate::create($validated);

        return redirect()->route('dpc.templates.index')
            ->with('success', 'Document PDF Template created successfully.');
    }

    public function edit(DocumentTemplate $template)
    {
        abort_if($template->clinic_id !== Auth::user()->clinic_id, 403);
        return view('document.templates.edit', compact('template'));
    }

    public function update(Request $request, DocumentTemplate $template)
    {
        abort_if($template->clinic_id !== Auth::user()->clinic_id, 403);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'margin_top' => 'nullable|integer|min:0',
            'margin_bottom' => 'nullable|integer|min:0',
            'margin_left' => 'nullable|integer|min:0',
            'margin_right' => 'nullable|integer|min:0',
            'disclaimer_text' => 'nullable|string',
            'header_image' => 'nullable|image|max:2048',
            'footer_image' => 'nullable|image|max:2048',
            'is_active' => 'boolean',
        ]);

        if ($request->hasFile('header_image')) {
            $validated['header_logo_path'] = $request->file('header_image')->store('templates/logos', 'public');
        }
        if ($request->hasFile('footer_image')) {
            $validated['footer_logo_path'] = $request->file('footer_image')->store('templates/logos', 'public');
        }

        $validated['is_active'] = $request->boolean('is_active');

        $template->update($validated);

        return redirect()->route('dpc.templates.index')
            ->with('success', 'Template berhasil diperbarui.');
    }

    public function destroy(DocumentTemplate $template)
    {
        abort_if($template->clinic_id !== Auth::user()->clinic_id, 403);

        // Delete header and footer images from storage
        if ($template->header_logo_path) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($template->header_logo_path);
        }
        if ($template->footer_logo_path) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($template->footer_logo_path);
        }

        $template->forceDelete();

        return redirect()->route('dpc.templates.index')
            ->with('success', 'Template berhasil dihapus permanently.');
    }

    public function deleteAll()
    {
        $clinicId = Auth::user()->clinic_id;
        $templates = DocumentTemplate::where('clinic_id', $clinicId)->get();

        foreach ($templates as $template) {
            if ($template->header_logo_path) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($template->header_logo_path);
            }
            if ($template->footer_logo_path) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($template->footer_logo_path);
            }
            $template->forceDelete();
        }

        return redirect()->route('dpc.templates.index')
            ->with('success', 'Semua template berhasil dihapus permanently.');
    }
}
