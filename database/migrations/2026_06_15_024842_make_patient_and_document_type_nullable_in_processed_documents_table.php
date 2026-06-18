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
        Schema::table('processed_documents', function (Blueprint $table) {
            $table->dropForeign(['patient_id']);
            $table->dropForeign(['document_type_id']);
        });

        Schema::table('processed_documents', function (Blueprint $table) {
            $table->foreignId('patient_id')->nullable()->change();
            $table->foreignId('document_type_id')->nullable()->change();
        });

        Schema::table('processed_documents', function (Blueprint $table) {
            $table->foreign('patient_id')->references('id')->on('patients')->nullOnDelete();
            $table->foreign('document_type_id')->references('id')->on('document_types')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('processed_documents', function (Blueprint $table) {
            $table->dropForeign(['patient_id']);
            $table->dropForeign(['document_type_id']);
        });

        Schema::table('processed_documents', function (Blueprint $table) {
            $table->foreignId('patient_id')->nullable(false)->change();
            $table->foreignId('document_type_id')->nullable(false)->change();
        });

        Schema::table('processed_documents', function (Blueprint $table) {
            $table->foreign('patient_id')->references('id')->on('patients')->cascadeOnDelete();
            $table->foreign('document_type_id')->references('id')->on('document_types')->cascadeOnDelete();
        });
    }
};
