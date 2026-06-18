<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('follow_up_visits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('follow_up_schedule_id')->constrained('follow_up_schedules')->cascadeOnDelete();
            $table->foreignId('examination_id')->constrained('examinations')->cascadeOnDelete();
            $table->foreignId('patient_id')->constrained('patients')->cascadeOnDelete();
            $table->foreignId('clinic_id')->constrained('clinics')->cascadeOnDelete();
            $table->foreignId('examined_by')->nullable()->constrained('users')->nullOnDelete();
            $table->date('visit_date');

            // Visus at follow-up
            $table->string('visus_od', 20)->nullable()->comment('Visus OD saat kontrol');
            $table->string('visus_os', 20)->nullable()->comment('Visus OS saat kontrol');

            // Clinical findings
            $table->text('complaints')->nullable()->comment('Keluhan pasien');
            $table->foreignId('lens_condition_id')->nullable()->constrained('lens_conditions')->nullOnDelete();
            $table->text('lens_condition_notes')->nullable();
            $table->text('doctor_notes')->nullable()->comment('Catatan dokter');

            // Status
            $table->foreignId('follow_up_status_id')->constrained('follow_up_statuses');
            $table->date('rescheduled_to')->nullable();
            $table->text('reschedule_reason')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index('visit_date');
            $table->index('patient_id');
            $table->index('follow_up_schedule_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('follow_up_visits');
    }
};
