<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\Clinic;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoReceptionistSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the demo clinic
        $clinic = Clinic::where('email', 'clinic@smart-medical.local')->first();

        if (!$clinic) {
            // Try to find clinic by user email
            $clinicOwner = User::where('email', 'clinic@smart-medical.local')->first();
            if ($clinicOwner) {
                $clinic = $clinicOwner->clinic;
            }
        }

        if (!$clinic) {
            $this->command->error('❌ Demo clinic not found. Please run DemoClinicSeeder first.');
            return;
        }

        // Check if receptionist already exists
        if (User::where('email', 'receptionist@smart-medical.local')->exists()) {
            $this->command->info('⚠️  Receptionist already exists');
            return;
        }

        // Create receptionist user for the clinic
        User::create([
            'name' => 'سارة أحمد',
            'email' => 'receptionist@smart-medical.local',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'role' => UserRole::Receptionist,
            'clinic_id' => $clinic->id,
        ]);

        $this->command->info('✅ Demo receptionist created: receptionist@smart-medical.local / password');
        $this->command->info("   Assigned to clinic: {$clinic->name}");
    }
}
