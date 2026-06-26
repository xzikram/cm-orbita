<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            ClinicSeeder::class,
            RolePermissionSeeder::class,
            UserSeeder::class,
            MasterDataSeeder::class,
            // SampleDataSeeder::class,
        ]);
    }
}
