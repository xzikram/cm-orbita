<?php

namespace Database\Seeders;

use App\Models\Clinic;
use App\Models\User;
use App\Models\Doctor;
use App\Models\RefractionOptician;
use App\Models\MedicalAssistant;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $clinic = Clinic::first();

        // Super Admin
        $superAdmin = User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@cfms.test',
            'password' => Hash::make('password', ['memory' => 65536, 'time' => 4, 'threads' => 1]),
            'clinic_id' => $clinic->id,
            'phone' => '081200000001',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
        $superAdmin->assignRole('super-admin');

        // Admin Klinik
        $adminKlinik = User::create([
            'name' => 'Admin Klinik',
            'email' => 'admin@cfms.test',
            'password' => Hash::make('password'),
            'clinic_id' => $clinic->id,
            'phone' => '081200000002',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
        $adminKlinik->assignRole('admin-klinik');

        // Dokter
        $dokterUser = User::create([
            'name' => 'dr. Siti Rahayu, Sp.M',
            'email' => 'dokter@cfms.test',
            'password' => Hash::make('password'),
            'clinic_id' => $clinic->id,
            'phone' => '081200000003',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
        $dokterUser->assignRole('dokter');

        Doctor::create([
            'clinic_id' => $clinic->id,
            'user_id' => $dokterUser->id,
            'name' => 'dr. Siti Rahayu, Sp.M',
            'sip_number' => 'SIP-001/2024',
            'specialization' => 'Spesialis Mata',
            'phone' => '081200000003',
            'email' => 'dokter@cfms.test',
            'is_active' => true,
        ]);

        // Dokter 2
        $dokterUser2 = User::create([
            'name' => 'dr. Ahmad Fauzi, Sp.M',
            'email' => 'dokter2@cfms.test',
            'password' => Hash::make('password'),
            'clinic_id' => $clinic->id,
            'phone' => '081200000008',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
        $dokterUser2->assignRole('dokter');

        Doctor::create([
            'clinic_id' => $clinic->id,
            'user_id' => $dokterUser2->id,
            'name' => 'dr. Ahmad Fauzi, Sp.M',
            'sip_number' => 'SIP-002/2024',
            'specialization' => 'Spesialis Mata',
            'phone' => '081200000008',
            'email' => 'dokter2@cfms.test',
            'is_active' => true,
        ]);

        // MedAss
        $medAssUser = User::create([
            'name' => 'Dewi Lestari',
            'email' => 'medass@cfms.test',
            'password' => Hash::make('password'),
            'clinic_id' => $clinic->id,
            'phone' => '081200000004',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
        $medAssUser->assignRole('med-ass');

        MedicalAssistant::create([
            'clinic_id' => $clinic->id,
            'user_id' => $medAssUser->id,
            'name' => 'Dewi Lestari',
            'phone' => '081200000004',
            'email' => 'medass@cfms.test',
            'is_active' => true,
        ]);

        // RO
        $roUser = User::create([
            'name' => 'Budi Santoso, RO',
            'email' => 'ro@cfms.test',
            'password' => Hash::make('password'),
            'clinic_id' => $clinic->id,
            'phone' => '081200000005',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
        $roUser->assignRole('ro');

        RefractionOptician::create([
            'clinic_id' => $clinic->id,
            'user_id' => $roUser->id,
            'name' => 'Budi Santoso, RO',
            'sip_number' => 'SIPRO-001/2024',
            'phone' => '081200000005',
            'email' => 'ro@cfms.test',
            'is_active' => true,
        ]);

        // Petugas Follow Up
        $petugasFU = User::create([
            'name' => 'Rina Wulandari',
            'email' => 'followup@cfms.test',
            'password' => Hash::make('password'),
            'clinic_id' => $clinic->id,
            'phone' => '081200000006',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
        $petugasFU->assignRole('petugas-follow-up');
    }
}
