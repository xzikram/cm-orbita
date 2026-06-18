<?php

namespace Database\Seeders;

use App\Models\Clinic;
use Illuminate\Database\Seeder;

class ClinicSeeder extends Seeder
{
    public function run(): void
    {
        Clinic::create([
            'name' => 'Klinik Mata Nusantara',
            'code' => 'KMN-001',
            'address' => 'Jl. Kesehatan No. 1, Jakarta Pusat',
            'phone' => '021-12345678',
            'email' => 'info@klinikmata.id',
            'is_active' => true,
            'settings' => [
                'timezone' => 'Asia/Jakarta',
                'working_hours' => '08:00 - 17:00',
                'working_days' => ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'],
            ],
        ]);
    }
}
