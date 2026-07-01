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
        Schema::table('document_deliveries', function (Blueprint $table) {
            $table->foreignId('processed_document_id')
                ->nullable()
                ->after('email_template_id')
                ->constrained('processed_documents')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('document_deliveries', function (Blueprint $table) {
            $table->dropForeign(['processed_document_id']);
            $table->dropColumn('processed_document_id');
        });
    }
};
