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
        // Hubungkan data lama berdasarkan pencocokan nama file, generated path, atau UUID
        \Illuminate\Support\Facades\DB::statement("
            UPDATE document_deliveries d
            JOIN processed_documents p ON (
                d.attachment_name = p.original_filename 
                OR d.attachment_name = SUBSTRING_INDEX(p.generated_file_path, '/', -1)
                OR d.attachment_path LIKE CONCAT('%', p.uuid, '%')
            )
            SET d.processed_document_id = p.id
            WHERE d.processed_document_id IS NULL
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
