<?php

namespace App\Modules\Document\Controllers;

use App\Models\DocumentTemplate;
use App\Models\DocumentType;
use App\Models\Patient;
use App\Models\ProcessedDocument;
use App\Modules\Document\Services\PdfGeneratorService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class DocumentProcessingController extends Controller
{
    public function __construct(protected PdfGeneratorService $pdfService) {}

    public function index()
    {
        $documents = ProcessedDocument::with(['patient', 'documentType', 'documentTemplate', 'creator'])
            ->where('clinic_id', Auth::user()->clinic_id)
            ->latest()
            ->paginate(config('cfms.per_page'));

        return view('document.processing.index', compact('documents'));
    }

    public function create()
    {
        $clinicId = Auth::user()->clinic_id;
        $patients = Patient::where('clinic_id', $clinicId)->active()->orderBy('name')->get();
        $documentTypes = DocumentType::where('clinic_id', $clinicId)->active()->orderBy('name')->get();
        $templates = DocumentTemplate::where('clinic_id', $clinicId)->active()->orderBy('name')->get();

        return view('document.processing.create', compact('patients', 'documentTypes', 'templates'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'patient_id' => 'nullable|exists:patients,id',
            'document_type_id' => 'nullable|exists:document_types,id',
            'document_template_id' => 'required|exists:document_templates,id',
            'original_pdf' => 'required|file|mimes:pdf|max:20480', // 20MB limit
        ]);

        $patient = $request->patient_id ? Patient::find($request->patient_id) : null;
        $template = DocumentTemplate::findOrFail($request->document_template_id);

        try {
            $processedDoc = $this->pdfService->processDocument(
                $request->file('original_pdf'),
                $patient,
                $template,
                $request->document_type_id,
                Auth::id()
            );

            return redirect()->route('dpc.processing.show', $processedDoc->id)
                ->with('success', 'Document successfully processed and wrapped.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Failed to process document: ' . $e->getMessage());
        }
    }

    public function show(ProcessedDocument $processing)
    {
        abort_if($processing->clinic_id !== Auth::user()->clinic_id, 403);
        $processing->load(['patient', 'documentType', 'documentTemplate', 'creator']);

        return view('document.processing.show', compact('processing'));
    }

    public function destroy(ProcessedDocument $processing)
    {
        abort_unless(Auth::user()->hasAnyRole(['super-admin', 'admin-klinik']), 403, 'Unauthorized action.');
        abort_if($processing->clinic_id !== Auth::user()->clinic_id, 403);

        // Delete original and generated files from disk
        if ($processing->original_file_path) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($processing->original_file_path);
        }
        if ($processing->generated_file_path) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($processing->generated_file_path);
        }

        $processing->forceDelete();

        return redirect()->route('dpc.processing.index')
            ->with('success', 'Dokumen berhasil dihapus permanently.');
    }

    public function deleteAll()
    {
        abort_unless(Auth::user()->hasAnyRole(['super-admin', 'admin-klinik']), 403, 'Unauthorized action.');
        $clinicId = Auth::user()->clinic_id;
        $documents = ProcessedDocument::where('clinic_id', $clinicId)->get();

        foreach ($documents as $doc) {
            if ($doc->original_file_path) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($doc->original_file_path);
            }
            if ($doc->generated_file_path) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($doc->generated_file_path);
            }
            $doc->forceDelete();
        }

        return redirect()->route('dpc.processing.index')
            ->with('success', 'Semua data dokumen berhasil dihapus permanently.');
    }
}
