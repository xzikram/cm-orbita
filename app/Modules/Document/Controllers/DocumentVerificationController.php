<?php

namespace App\Modules\Document\Controllers;

use App\Models\ProcessedDocument;
use Illuminate\Routing\Controller;

class DocumentVerificationController extends Controller
{
    /**
     * Public endpoint to verify a document by its UUID.
     * Accessible without authentication.
     */
    public function verify($uuid)
    {
        $document = ProcessedDocument::with(['patient', 'clinic', 'documentType'])
            ->where('uuid', $uuid)
            ->first();

        if (!$document) {
            return view('document.verification.invalid');
        }

        return view('document.verification.valid', compact('document'));
    }
}
