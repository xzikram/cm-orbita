<?php

namespace App\Modules\Document\Services;

use App\Models\DocumentType;
use App\Models\ProcessedDocument;
use Illuminate\Support\Facades\DB;

class DocumentNumberService
{
    /**
     * Generate a sequential document number.
     * Format: JEC-[CODE]-[YEAR]-[SEQUENCE]
     * Sequence does not reset.
     */
    public function generateNumber(?DocumentType $type): string
    {
        return DB::transaction(function () use ($type) {
            $query = ProcessedDocument::withTrashed();
            if ($type) {
                $query->where('document_type_id', $type->id);
            } else {
                $query->whereNull('document_type_id');
            }

            // Find the last document, get its sequence
            $lastDocument = $query->lockForUpdate() // Prevent concurrent generation duplicates
                ->orderBy('document_number', 'desc')
                ->first();

            $sequence = 1;

            if ($lastDocument && preg_match('/-(\d+)$/', $lastDocument->document_number, $matches)) {
                $sequence = (int) $matches[1] + 1;
            }

            $year = date('Y');
            $code = $type ? strtoupper($type->code) : 'GEN';
            $paddedSequence = str_pad($sequence, 6, '0', STR_PAD_LEFT);

            return "JEC-{$code}-{$year}-{$paddedSequence}";
        });
    }
}
