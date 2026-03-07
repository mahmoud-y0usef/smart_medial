<?php

namespace Database\Seeders;

use App\Enums\ApprovalStatus;
use App\Enums\UserRole;
use App\Models\Clinic;
use App\Models\Doctor;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoClinicSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create clinic owner user
        $clinicOwner = User::create([
            'name' => 'د. أحمد محمود',
            'email' => 'clinic@smart-medical.local',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'role' => UserRole::Doctor,
        ]);

        // Create approved clinic
        $clinic = Clinic::create([
            'user_id' => $clinicOwner->id,
            'name' => 'عيادة الشفاء التخصصية',
            'name_en' => 'Al-Shifa Specialized Clinic',
            'license_number' => 'LIC-2024-001',
            'phone' => '+201234567890',
            'address' => 'شارع الجمهورية، المنصورة، مصر',
            'city' => 'المنصورة',
            'latitude' => 31.0364,
            'longitude' => 31.3803,
            'specializations' => ['باطنة', 'أطفال', 'جلدية'],
            'working_hours' => [
                'saturday' => ['09:00-17:00'],
                'sunday' => ['09:00-17:00'],
                'monday' => ['09:00-17:00'],
                'tuesday' => ['09:00-17:00'],
                'wednesday' => ['09:00-17:00'],
                'thursday' => ['09:00-13:00'],
            ],
            'approval_status' => ApprovalStatus::Approved,
            'approved_at' => now(),
            'is_active' => true,
        ]);

        // Create doctor profile
        Doctor::create([
            'clinic_id' => $clinic->id,
            'name' => 'د. أحمد محمود',
            'name_en' => 'Dr. Ahmed Mahmoud',
            'phone' => '+201234567890',
            'specialization' => 'باطنة عامة',
            'license_number' => 'MED-2024-12345',
            'is_primary' => true,
        ]);

        // Create receptionist user for the clinic
        User::create([
            'name' => 'سارة أحمد',
            'email' => 'receptionist@smart-medical.local',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'role' => UserRole::Receptionist,
            'clinic_id' => $clinic->id,
        ]);

        $this->command->info('✅ Demo clinic created: clinic@smart-medical.local / password');
        $this->command->info('✅ Demo receptionist created: receptionist@smart-medical.local / password');
    }
}
