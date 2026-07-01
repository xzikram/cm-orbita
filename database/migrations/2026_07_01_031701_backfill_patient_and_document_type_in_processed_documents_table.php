<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Sinkronisasi data patient_id dan document_type_id dari document_deliveries ke processed_documents
        \Illuminate\Support\Facades\DB::statement("
            UPDATE processed_documents p
            JOIN document_deliveries d ON p.id = d.processed_document_id
            SET p.patient_id = COALESCE(p.patient_id, d.patient_id),
                p.document_type_id = COALESCE(p.document_type_id, d.document_type_id)
            WHERE p.patient_id IS NULL OR p.document_type_id IS NULL
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
