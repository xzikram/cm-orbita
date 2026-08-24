<?php

use App\Models\InpatientFollowUp;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        // Bersihkan data rawat inap dari SIM RS dengan tanggal pulang sebelum 20 Agustus 2026
        InpatientFollowUp::where('source', 'simrs')
            ->whereDate('discharge_date', '<', '2026-08-20')
            ->forceDelete();
    }

    public function down(): void
    {
        // No reverse needed
    }
};
