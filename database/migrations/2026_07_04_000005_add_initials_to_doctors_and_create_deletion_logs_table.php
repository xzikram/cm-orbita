<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('doctors', function (Blueprint $table) {
            $table->string('initials', 10)->nullable()->after('name');
            $table->index('initials');
        });

        Schema::create('deletion_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('model_type');
            $table->unsignedBigInteger('model_id');
            $table->string('model_name');
            $table->string('model_identifier');
            $table->text('reason');
            $table->timestamp('created_at')->useCurrent();

            $table->index(['model_type', 'model_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('deletion_logs');

        Schema::table('doctors', function (Blueprint $table) {
            $table->dropIndex(['initials']);
            $table->dropColumn(['initials']);
        });
    }
};
