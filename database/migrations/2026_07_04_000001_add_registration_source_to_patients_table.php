<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            $table->string('registration_source', 50)->default('admin')->after('is_active');
            $table->unsignedBigInteger('registration_source_id')->nullable()->after('registration_source');

            $table->index('registration_source');
            $table->index('registration_source_id');
        });
    }

    public function down(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            $table->dropIndex(['registration_source']);
            $table->dropIndex(['registration_source_id']);
            $table->dropColumn(['registration_source', 'registration_source_id']);
        });
    }
};
