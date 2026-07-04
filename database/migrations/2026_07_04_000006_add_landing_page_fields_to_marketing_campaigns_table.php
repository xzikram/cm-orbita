<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('marketing_campaigns', function (Blueprint $table) {
            $table->string('landing_page_type', 20)->default('direct')->after('is_active');
            $table->text('description')->nullable()->after('landing_page_type');
            $table->string('video_url')->nullable()->after('description');
            $table->string('brochure_image_path')->nullable()->after('video_url');
            $table->text('testimonials')->nullable()->after('brochure_image_path')->comment('JSON array of testimonials');
            $table->text('benefits')->nullable()->after('testimonials')->comment('JSON array of benefits');
        });
    }

    public function down(): void
    {
        Schema::table('marketing_campaigns', function (Blueprint $table) {
            $table->dropColumn([
                'landing_page_type',
                'description',
                'video_url',
                'brochure_image_path',
                'testimonials',
                'benefits',
            ]);
        });
    }
};
