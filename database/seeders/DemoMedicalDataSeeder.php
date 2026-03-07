<?php

namespace Database\Seeders;

use App\Models\Consultation;
use App\Models\Doctor;
use App\Models\Medicine;
use App\Models\Patient;
use App\Models\Prescription;
use App\Models\PrescriptionMedicine;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoMedicalDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get or create demo patient
        $patientUser = User::firstOrCreate(
            ['email' => 'patient@smart-medical.local'],
            [
                'name' => 'محمد علي',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'role' => \App\Enums\UserRole::Patient,
            ]
        );

        $patient = Patient::firstOrCreate(
            ['user_id' => $patientUser->id],
            [
                'phone' => '+201234567891',
                'name' => 'محمد علي',
                'date_of_birth' => now()->subYears(30),
                'gender' => 'male',
                'blood_type' => 'A+',
                'national_id' => '12345678901234',
            ]
        );

        // Get the demo doctor
        $doctor = Doctor::first();

        if (!$doctor) {
            $this->command->error('❌ Please run DemoClinicSeeder first');
            return;
        }

        // Create consultations with prescriptions for past days
        for ($i = 0; $i < 5; $i++) {
            $consultationDate = today()->subDays($i);
            
            $consultation = Consultation::create([
                'patient_id' => $patient->id,
                'doctor_id' => $doctor->id,
                'chief_complaint' => ['التهاب الحلق', 'ألم في المعدة', 'حساسية', 'صداع', 'ألم في الظهر'][$i],
                'diagnosis' => ['التهاب اللوزتين', 'عسر الهضم', 'حساسية موسمية', 'صداع نصفي', 'ألم عضلي'][$i],
                'treatment_plan' => 'أدوية حسب الروشتة',
                'consultation_date' => $consultationDate,
               'notes' => 'متابعة بعد أسبوع',
            ]);

            $prescription = Prescription::create([
                'consultation_id' => $consultation->id,
                'patient_id' => $patient->id,
                'doctor_id' => $doctor->id,
                'prescription_number' => 'RX-' . $consultationDate->format('Ymd') . '-' . str_pad($i + 1, 4, '0', STR_PAD_LEFT),
                'status' => $i === 0 ? 'pending' : 'dispensed',
                'notes' => 'تعليمات خاصة للصيدلي',
            ]);

            // Add 2-3 random medicines to each prescription
            $medicineCount = rand(2, 3);
            $medicines = Medicine::inRandomOrder()->take($medicineCount)->get();
            
            foreach ($medicines as $medicine) {
                PrescriptionMedicine::create([
                    'prescription_id' => $prescription->id,
                    'medicine_id' => $medicine->id,
                    'dosage' => ['قرص واحد', 'قرصين', 'كبسولة واحدة'][rand(0, 2)],
                    'frequency' => ['مرة يومياً', 'مرتين يومياً', 'ثلاث مرات يومياً'][rand(0, 2)],
                    'duration' => ['أسبوع', 'أسبوعين', '5 أيام', '10 أيام'][rand(0, 3)],
                    'instructions' => 'بعد الأكل',
                    'quantity' => rand(7, 30),
                ]);
            }
        }

        $this->command->info('✅ Created demo medical data:');
        $this->command->info('   - 5 Consultations');
        $this->command->info('   - 5 Prescriptions (4 dispensed, 1 pending)');
        $this->command->info('   - Patient: patient@smart-medical.local / password');
    }
}
