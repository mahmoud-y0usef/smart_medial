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
            AdminSeeder::class,
            MedicineSeeder::class,
            DemoClinicSeeder::class,
            DemoPharmacySeeder::class,
            // DemoMedicalDataSeeder::class, // TODO: Fix field names to match migrations
        ]);
    }
}
