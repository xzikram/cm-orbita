<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('follow_up_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('examination_id')->constrained('examinations')->cascadeOnDelete();
            $table->foreignId('patient_id')->constrained('patients')->cascadeOnDelete();
            $table->foreignId('clinic_id')->constrained('clinics')->cascadeOnDelete();
            $table->string('label')->comment('e.g., Hari ke-1, 1 Bulan');
            $table->integer('interval_days')->comment('Days from examination date');
            $table->date('scheduled_date');
            $table->integer('sequence')->comment('Order in schedule: 1,2,3...');
            $table->enum('status', ['pending', 'completed', 'missed', 'rescheduled', 'cancelled'])->default('pending');
            $table->date('rescheduled_date')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('reminder_sent')->default(false);
            $table->timestamp('reminder_sent_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['scheduled_date', 'status']);
            $table->index('patient_id');
            $table->index('clinic_id');
            $table->index('examination_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('follow_up_schedules');
    }
};
