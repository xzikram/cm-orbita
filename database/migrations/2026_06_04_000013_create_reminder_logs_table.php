<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reminder_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reminder_id')->constrained('reminders')->cascadeOnDelete();
            $table->string('channel');
            $table->string('provider')->nullable()->comment('fonnte, wablas, meta, log');
            $table->enum('status', ['success', 'failed']);
            $table->string('recipient_phone', 20);
            $table->text('message_sent')->nullable();
            $table->text('response')->nullable()->comment('API response from provider');
            $table->text('error_message')->nullable();
            $table->integer('response_code')->nullable();
            $table->decimal('duration_ms', 10, 2)->nullable()->comment('Response time in milliseconds');
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['reminder_id', 'status']);
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reminder_logs');
    }
};
