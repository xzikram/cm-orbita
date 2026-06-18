<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Document Templates
        Schema::create('document_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('clinic_id')->nullable()->constrained('clinics')->nullOnDelete();
            $table->string('code')->unique();
            $table->string('name');
            $table->string('header_logo_path')->nullable();
            $table->string('footer_logo_path')->nullable();
            $table->string('cover_design_type')->default('standard');
            $table->text('disclaimer_text')->nullable();
            $table->string('watermark_text')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        // 2. Processed Documents (DPC Log/Archive)
        Schema::create('processed_documents', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('document_number')->unique();
            $table->foreignId('clinic_id')->constrained('clinics')->cascadeOnDelete();
            $table->foreignId('patient_id')->constrained('patients')->cascadeOnDelete();
            $table->foreignId('document_type_id')->constrained('document_types')->cascadeOnDelete();
            $table->foreignId('document_template_id')->nullable()->constrained('document_templates')->nullOnDelete();
            
            $table->string('original_file_path')->comment('Path to the raw uploaded PDF');
            $table->string('generated_file_path')->nullable()->comment('Path to the wrapped final PDF');
            
            $table->string('status')->default('draft')->comment('draft, generated, signed, archived');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['clinic_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('processed_documents');
        Schema::dropIfExists('document_templates');
    }
};
