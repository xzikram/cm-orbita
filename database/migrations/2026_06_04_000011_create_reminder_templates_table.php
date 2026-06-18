<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reminder_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('clinic_id')->nullable()->constrained('clinics')->nullOnDelete();
            $table->string('name');
            $table->string('type')->default('follow_up')->comment('follow_up, appointment, custom');
            $table->string('channel')->default('whatsapp');
            $table->text('content')->comment('Template message with placeholders: {patient_name}, {doctor_name}, {date}, {clinic_name}');
            $table->json('variables')->nullable()->comment('Available template variables');
            $table->boolean('is_default')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['type', 'channel', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reminder_templates');
    }
};
