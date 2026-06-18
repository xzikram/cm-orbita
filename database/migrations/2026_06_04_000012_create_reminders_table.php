<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reminders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('clinic_id')->constrained('clinics')->cascadeOnDelete();
            $table->foreignId('follow_up_schedule_id')->nullable()->constrained('follow_up_schedules')->nullOnDelete();
            $table->foreignId('reminder_template_id')->nullable()->constrained('reminder_templates')->nullOnDelete();
            $table->string('channel')->default('whatsapp');
            $table->string('recipient_type')->comment('patient, doctor, ro, medass');
            $table->string('recipient_name');
            $table->string('recipient_phone', 20);
            $table->text('message');
            $table->enum('status', ['pending', 'sent', 'failed', 'cancelled'])->default('pending');
            $table->timestamp('scheduled_at');
            $table->timestamp('sent_at')->nullable();
            $table->integer('retry_count')->default(0);
            $table->text('error_message')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'scheduled_at']);
            $table->index('clinic_id');
            $table->index('follow_up_schedule_id');
            $table->index('channel');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reminders');
    }
};
