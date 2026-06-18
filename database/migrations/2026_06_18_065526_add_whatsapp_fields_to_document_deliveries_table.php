<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('document_deliveries', function (Blueprint $table) {
            $table->string('recipient_email')->nullable()->change();
            $table->string('subject')->nullable()->change();
            $table->string('channel')->default('email')->after('sent_by');
            $table->string('recipient_phone')->nullable()->after('recipient_email');
            $table->string('attachment_path')->nullable()->after('attachment_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('document_deliveries', function (Blueprint $table) {
            $table->string('recipient_email')->nullable(false)->change();
            $table->string('subject')->nullable(false)->change();
            $table->dropColumn(['channel', 'recipient_phone', 'attachment_path']);
        });
    }
};
