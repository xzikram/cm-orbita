<?php

use App\Models\InpatientFollowUp;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Bersihkan data rawat inap lama sebelum 1 Agustus 2026 yang berasal dari SIM RS
        InpatientFollowUp::where('source', 'simrs')
            ->whereDate('discharge_date', '<', '2026-08-01')
            ->forceDelete();
    }

    public function down(): void
    {
        // No reverse needed
    }
};
