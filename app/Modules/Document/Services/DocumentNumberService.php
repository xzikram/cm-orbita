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
            $year = date('Y');
            $code = $type ? strtoupper($type->code) : 'GEN';
            $prefix = "JEC-{$code}-{$year}-";

            // Cari nomor dokumen terbesar yang cocok dengan prefix ini,
            // termasuk yang sudah di-soft delete, agar tidak terjadi duplikasi.
            $lastDocument = ProcessedDocument::withTrashed()
                ->where('document_number', 'like', $prefix . '%')
                ->lockForUpdate()
                ->orderBy('document_number', 'desc')
                ->first();

            $sequence = 1;

            if ($lastDocument && preg_match('/(\d+)$/', $lastDocument->document_number, $matches)) {
                $sequence = (int) $matches[1] + 1;
            }

            $paddedSequence = str_pad($sequence, 6, '0', STR_PAD_LEFT);

            return "{$prefix}{$paddedSequence}";
        });
    }
}

