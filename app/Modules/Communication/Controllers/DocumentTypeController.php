<?php

namespace App\Modules\Communication\Controllers;

use App\Core\Services\AuditLogService;
use App\Models\DocumentType;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class DocumentTypeController extends Controller
{
    public function __construct(protected AuditLogService $auditLogService) {}

    public function index()
    {
        $types = DocumentType::where('clinic_id', Auth::user()->clinic_id)
            ->latest()
            ->paginate(config('cfms.per_page'));

        return view('communication.document-types.index', compact('types'));
    }

    public function create()
    {
        return view('communication.document-types.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:document_types,code',
            'name' => 'required|string|max:255',
            'is_active' => 'boolean',
        ]);

        $validated['clinic_id'] = Auth::user()->clinic_id;

        $type = DocumentType::create($validated);
        $this->auditLogService->logCreated('DocumentType', $type->id, ['code' => $type->code, 'name' => $type->name]);

        return redirect()->route('communication.document-types.index')
            ->with('success', 'Document Type created successfully.');
    }

    public function edit(DocumentType $documentType)
    {
        abort_if($documentType->clinic_id !== Auth::user()->clinic_id, 403);
        return view('communication.document-types.edit', compact('documentType'));
    }

    public function update(Request $request, DocumentType $documentType)
    {
        abort_if($documentType->clinic_id !== Auth::user()->clinic_id, 403);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'is_active' => 'boolean',
        ]);

        $old = ['name' => $documentType->name];
        $documentType->update($validated);
        $this->auditLogService->logUpdated('DocumentType', $documentType->id, $old, ['name' => $documentType->name]);

        return redirect()->route('communication.document-types.index')
            ->with('success', 'Document Type updated successfully.');
    }

    public function destroy(DocumentType $documentType)
    {
        abort_if($documentType->clinic_id !== Auth::user()->clinic_id, 403);
        $documentType->delete();
        $this->auditLogService->logDeleted('DocumentType', $documentType->id, ['name' => $documentType->name]);

        return redirect()->route('communication.document-types.index')
            ->with('success', 'Document Type deleted successfully.');
    }

    public function deleteAll()
    {
        $clinicId = Auth::user()->clinic_id;
        $types = DocumentType::where('clinic_id', $clinicId)->get();
        
        foreach ($types as $type) {
            $type->delete();
            $this->auditLogService->logDeleted('DocumentType', $type->id, ['name' => $type->name]);
        }

        return redirect()->route('communication.document-types.index')
            ->with('success', 'All document types deleted successfully.');
    }
}
