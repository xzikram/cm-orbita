<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\User;

echo "=== MEMULAI PEMBERSIHAN DATA DUMMY DATABASE ===\n";

try {
    DB::beginTransaction();
    
    // 1. Menonaktifkan Foreign Key Constraints sementara
    echo "Menonaktifkan batasan foreign key...\n";
    Schema::disableForeignKeyConstraints();
    
    // 2. Mengosongkan tabel transaksi dan log terkait
    echo "Mengosongkan tabel reminder_logs...\n";
    DB::table('reminder_logs')->truncate();
    
    echo "Mengosongkan tabel reminders...\n";
    DB::table('reminders')->truncate();
    
    echo "Mengosongkan tabel follow_up_visits...\n";
    DB::table('follow_up_visits')->truncate();
    
    echo "Mengosongkan tabel follow_up_schedules...\n";
    DB::table('follow_up_schedules')->truncate();
    
    echo "Mengosongkan tabel examinations...\n";
    DB::table('examinations')->truncate();
    
    echo "Mengosongkan tabel document_deliveries...\n";
    DB::table('document_deliveries')->truncate();
    
    echo "Mengosongkan tabel processed_documents...\n";
    DB::table('processed_documents')->truncate();
    
    // 3. Mengosongkan tabel master pasien, dokter, dll.
    echo "Mengosongkan tabel patients...\n";
    DB::table('patients')->truncate();
    
    echo "Mengosongkan tabel doctors...\n";
    DB::table('doctors')->truncate();
    
    echo "Mengosongkan tabel medical_assistants...\n";
    DB::table('medical_assistants')->truncate();
    
    echo "Mengosongkan tabel refraction_opticians...\n";
    DB::table('refraction_opticians')->truncate();
    
    // 4. Menghapus User dengan role dummy (dokter, ro, med-ass, petugas-follow-up)
    echo "Menghapus akun User dummy (dokter, ro, med-ass, petugas-follow-up)...\n";
    $dummyUsers = User::role(['dokter', 'ro', 'med-ass', 'petugas-follow-up'])->get();
    foreach ($dummyUsers as $user) {
        echo "Menghapus user: {$user->email}\n";
        $user->delete();
    }
    
    // 5. Mengaktifkan kembali Foreign Key Constraints
    echo "Mengaktifkan kembali batasan foreign key...\n";
    Schema::enableForeignKeyConstraints();
    
    DB::commit();
    echo "=== PROSES PEMBERSIHAN SELESAI DENGAN SUKSES ===\n";
} catch (\Exception $e) {
    DB::rollBack();
    Schema::enableForeignKeyConstraints();
    echo "Gagal membersihkan data: " . $e->getMessage() . "\n";
    exit(1);
}
