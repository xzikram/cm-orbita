<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            $table->boolean('is_downtime_entry')->default(false)->after('is_active');
            $table->string('nik', 16)->nullable()->after('name');
            $table->string('parent_spouse_name')->nullable()->after('phone');
            $table->string('emergency_contact_name')->nullable()->after('parent_spouse_name');
            $table->string('emergency_contact_phone', 20)->nullable()->after('emergency_contact_name');

            $table->index('is_downtime_entry');
            $table->index('nik');
        });

        Schema::table('examinations', function (Blueprint $table) {
            $table->boolean('is_downtime_entry')->default(false)->after('status');
            $table->enum('patient_status', ['Lama', 'Baru'])->nullable()->after('is_downtime_entry');
            $table->date('registration_date')->nullable()->after('patient_status');
            $table->string('registration_number')->nullable()->after('registration_date');
            $table->string('guarantor')->nullable()->after('registration_number');
            $table->string('service_unit')->nullable()->after('guarantor');
            $table->string('tindakan')->nullable()->after('service_unit');
            $table->string('queue_number')->nullable()->after('tindakan');
            $table->decimal('total_payment', 15, 2)->default(0.00)->after('queue_number');

            $table->index('is_downtime_entry');
            $table->index('registration_date');
            $table->index('registration_number');
        });
    }

    public function down(): void
    {
        Schema::table('examinations', function (Blueprint $table) {
            $table->dropIndex(['is_downtime_entry']);
            $table->dropIndex(['registration_date']);
            $table->dropIndex(['registration_number']);

            $table->dropColumn([
                'is_downtime_entry',
                'patient_status',
                'registration_date',
                'registration_number',
                'guarantor',
                'service_unit',
                'tindakan',
                'queue_number',
                'total_payment',
            ]);
        });

        Schema::table('patients', function (Blueprint $table) {
            $table->dropIndex(['is_downtime_entry']);
            $table->dropIndex(['nik']);

            $table->dropColumn([
                'is_downtime_entry',
                'nik',
                'parent_spouse_name',
                'emergency_contact_name',
                'emergency_contact_phone',
            ]);
        });
    }
};
