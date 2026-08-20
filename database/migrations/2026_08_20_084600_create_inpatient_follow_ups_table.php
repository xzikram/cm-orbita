<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inpatient_follow_ups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('clinic_id')->constrained('clinics')->cascadeOnDelete();
            $table->foreignId('patient_id')->nullable()->constrained('patients')->nullOnDelete();
            
            $table->string('registration_no')->nullable()->index();
            $table->string('medical_record_number')->index();
            $table->string('patient_name');
            $table->string('patient_phone')->nullable();
            $table->unsignedSmallInteger('patient_age')->nullable();
            $table->string('gender', 10)->nullable();
            
            $table->dateTime('admission_date')->nullable();
            $table->dateTime('discharge_date')->index();
            $table->date('follow_up_due_date')->index(); // Discharge date + 3 days
            
            $table->string('room_bed')->nullable();
            $table->string('doctor_dpjp')->nullable();
            $table->text('diagnosis_or_procedure')->nullable();
            
            $table->string('nurse_name')->nullable();
            $table->enum('status', ['pending', 'sent', 'completed', 'cancelled'])->default('pending')->index();
            $table->enum('source', ['simrs', 'manual'])->default('simrs');
            
            $table->text('whatsapp_message')->nullable();
            $table->string('whatsapp_message_id')->nullable();
            $table->dateTime('sent_at')->nullable();
            $table->foreignId('sent_by')->nullable()->constrained('users')->nullOnDelete();
            
            // 5 Poin Respon Evaluasi Klinis Pasien Pasca Pulang Ranap
            $table->text('response_complaints')->nullable(); // 1. Keluhan saat ini
            $table->string('response_medication_compliance')->nullable(); // 2. Kepatuhan penggunaan obat
            $table->text('response_side_effects')->nullable(); // 3. Efek samping obat
            $table->text('response_wound_condition')->nullable(); // 4. Kondisi luka operasi (kemerahan/bengkak/cairan)
            $table->text('response_vision_progress')->nullable(); // 5. Perubahan/kemajuan penglihatan
            
            $table->text('response_notes')->nullable();
            $table->boolean('needs_doctor_review')->default(false)->index();
            $table->dateTime('responded_at')->nullable();
            $table->foreignId('responded_by')->nullable()->constrained('users')->nullOnDelete();
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inpatient_follow_ups');
    }
};
