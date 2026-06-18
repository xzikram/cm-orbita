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
        Schema::table('document_templates', function (Blueprint $table) {
            $table->text('description')->nullable()->after('name');
            $table->integer('margin_top')->default(40)->after('footer_logo_path');
            $table->integer('margin_bottom')->default(30)->after('margin_top');
            $table->integer('margin_left')->default(20)->after('margin_bottom');
            $table->integer('margin_right')->default(20)->after('margin_left');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('document_templates', function (Blueprint $table) {
            $table->dropColumn(['description', 'margin_top', 'margin_bottom', 'margin_left', 'margin_right']);
        });
    }
};
