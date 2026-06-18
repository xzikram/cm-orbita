<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('examinations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('clinic_id')->constrained('clinics')->cascadeOnDelete();
            $table->foreignId('patient_id')->constrained('patients')->cascadeOnDelete();
            $table->foreignId('doctor_id')->constrained('doctors')->cascadeOnDelete();
            $table->foreignId('ro_id')->nullable()->constrained('refraction_opticians')->nullOnDelete();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->date('examination_date');

            // Mata Kanan (OD - Oculus Dexter)
            $table->decimal('od_sphere', 6, 2)->nullable();
            $table->decimal('od_cylinder', 6, 2)->nullable();
            $table->integer('od_axis')->nullable()->comment('0-180 degrees');
            $table->string('od_visus', 20)->nullable()->comment('e.g., 6/6, 6/12');

            // Mata Kiri (OS - Oculus Sinister)
            $table->decimal('os_sphere', 6, 2)->nullable();
            $table->decimal('os_cylinder', 6, 2)->nullable();
            $table->integer('os_axis')->nullable()->comment('0-180 degrees');
            $table->string('os_visus', 20)->nullable()->comment('e.g., 6/6, 6/12');

            // Additional info
            $table->string('lens_type')->nullable()->comment('Jenis lensa kontak');
            $table->string('lens_brand')->nullable();
            $table->string('lens_power_od', 20)->nullable();
            $table->string('lens_power_os', 20)->nullable();
            $table->text('clinical_notes')->nullable()->comment('Catatan klinis');
            $table->enum('status', ['active', 'completed', 'cancelled'])->default('active');

            $table->timestamps();
            $table->softDeletes();

            $table->index('clinic_id');
            $table->index('patient_id');
            $table->index('doctor_id');
            $table->index('examination_date');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('examinations');
    }
};
