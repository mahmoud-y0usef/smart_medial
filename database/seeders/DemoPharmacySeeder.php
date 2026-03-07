<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\Medicine;
use App\Models\Pharmacy;
use App\Models\Pharmacist;
use App\Models\PharmacyInventory;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoPharmacySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Check if pharmacy user already exists
        if (User::where('email', 'pharmacy@smart-medical.local')->exists()) {
            $this->command->info('⚠️  Pharmacy user already exists, skipping...');
            return;
        }

        // Create pharmacy owner user
        $pharmacyOwner = User::create([
            'name' => 'صيدلية النور',
            'email' => 'pharmacy@smart-medical.local',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'role' => UserRole::Pharmacy,
        ]);

        // Create pharmacy
        $pharmacy = Pharmacy::create([
            'user_id' => $pharmacyOwner->id,
            'name' => 'صيدلية النور',
            'name_en' => 'Al-Nour Pharmacy',
            'license_number' => 'PHARM-2024-001',
            'phone' => '+201234567899',
            'address' => 'شارع الثورة، المنصورة، مصر',
            'city' => 'المنصورة',
            'latitude' => 31.0404,
            'longitude' => 31.3785,
            'working_hours' => [
                'saturday' => ['09:00-22:00'],
                'sunday' => ['09:00-22:00'],
                'monday' => ['09:00-22:00'],
                'tuesday' => ['09:00-22:00'],
                'wednesday' => ['09:00-22:00'],
                'thursday' => ['09:00-22:00'],
                'friday' => ['14:00-22:00'],
            ],
            'is_active' => true,
        ]);

        // Create pharmacist profile
        Pharmacist::create([
            'user_id' => $pharmacyOwner->id,
            'pharmacy_id' => $pharmacy->id,
            'license_number' => 'PHARM-LIC-2024-001',
            'specialization' => 'صيدلي عام',
        ]);

        // Create demo medicines
        $medicines = [
            [
                'name_ar' => 'باراسيتامول 500مجم',
                'name_en' => 'Paracetamol 500mg',
                'scientific_name' => 'Paracetamol',
                'active_ingredient' => 'Paracetamol',
                'category' => 'Pain Relief',
                'form' => 'tablet',
                'strength' => '500mg',
                'manufacturer' => 'شركة الأدوية المصرية',
                'description' => 'مسكن للألم وخافض للحرارة',
                'requires_prescription' => false,
                'is_active' => true,
            ],
            [
                'name_ar' => 'أموكسيسيلين 500مجم',
                'name_en' => 'Amoxicillin 500mg',
                'scientific_name' => 'Amoxicillin',
                'active_ingredient' => 'Amoxicillin',
                'category' => 'Antibiotics',
                'form' => 'capsule',
                'strength' => '500mg',
                'manufacturer' => 'شركة جلاكسو',
                'description' => 'مضاد حيوي واسع الطيف',
                'requires_prescription' => true,
                'is_active' => true,
            ],
            [
                'name_ar' => 'أومفيل شراب',
                'name_en' => 'Omfeel Syrup',
                'scientific_name' => 'Paracetamol',
                'active_ingredient' => 'Paracetamol',
                'category' => 'Pain Relief',
                'form' => 'syrup',
                'strength' => '120mg/5ml',
                'manufacturer' => 'شركة الإسكندرية للأدوية',
                'description' => 'مسكن وخافض حرارة للأطفال',
                'requires_prescription' => false,
                'is_active' => true,
            ],
            [
                'name_ar' => 'فيتامين د 5000 وحدة',
                'name_en' => 'Vitamin D 5000 IU',
                'scientific_name' => 'Cholecalciferol',
                'active_ingredient' => 'Vitamin D3',
                'category' => 'Vitamins',
                'form' => 'capsule',
                'strength' => '5000 IU',
                'manufacturer' => 'شركة فاركو',
                'description' => 'مكمل غذائي فيتامين د',
                'requires_prescription' => false,
                'is_active' => true,
            ],
            [
                'name_ar' => 'بروفين 400مجم',
                'name_en' => 'Brufen 400mg',
                'scientific_name' => 'Ibuprofen',
                'active_ingredient' => 'Ibuprofen',
                'category' => 'Pain Relief',
                'form' => 'tablet',
                'strength' => '400mg',
                'manufacturer' => 'شركة إيبيكو',
                'description' => 'مسكن للألم ومضاد للالتهاب',
                'requires_prescription' => false,
                'is_active' => true,
            ],
            [
                'name_ar' => 'كونكور 5مجم',
                'name_en' => 'Concor 5mg',
                'scientific_name' => 'Bisoprolol',
                'active_ingredient' => 'Bisoprolol',
                'category' => 'Cardiovascular',
                'form' => 'tablet',
                'strength' => '5mg',
                'manufacturer' => 'شركة نايل',
                'description' => 'لعلاج ضغط الدم المرتفع',
                'requires_prescription' => true,
                'is_active' => true,
            ],
            [
                'name_ar' => 'جلوكوفاج 500مجم',
                'name_en' => 'Glucophage 500mg',
                'scientific_name' => 'Metformin',
                'active_ingredient' => 'Metformin HCl',
                'category' => 'Diabetes',
                'form' => 'tablet',
                'strength' => '500mg',
                'manufacturer' => 'شركة سيديكو',
                'description' => 'لعلاج السكري من النوع الثاني',
                'requires_prescription' => true,
                'is_active' => true,
            ],
            [
                'name_ar' => 'قطرة توبريكس للعين',
                'name_en' => 'Tobrex Eye Drops',
                'scientific_name' => 'Tobramycin',
                'active_ingredient' => 'Tobramycin',
                'category' => 'Ophthalmology',
                'form' => 'drops',
                'strength' => '0.3%',
                'manufacturer' => 'شركة الكون',
                'description' => 'مضاد حيوي للعين',
                'requires_prescription' => true,
                'is_active' => true,
            ],
            [
                'name_ar' => 'فيوسيدين مرهم',
                'name_en' => 'Fucidin Cream',
                'scientific_name' => 'Fusidic Acid',
                'active_ingredient' => 'Fusidic Acid',
                'category' => 'Dermatology',
                'form' => 'cream',
                'strength' => '2%',
                'manufacturer' => 'شركة ممفيس',
                'description' => 'مضاد حيوي موضعي',
                'requires_prescription' => false,
                'is_active' => true,
            ],
            [
                'name_ar' => 'أوجمنتين 1جم',
                'name_en' => 'Augmentin 1g',
                'scientific_name' => 'Amoxicillin + Clavulanic Acid',
                'active_ingredient' => 'Amoxicillin + Clavulanic Acid',
                'category' => 'Antibiotics',
                'form' => 'tablet',
                'strength' => '1000mg',
                'manufacturer' => 'شركة جلاكسو',
                'description' => 'مضاد حيوي قوي',
                'requires_prescription' => true,
                'is_active' => true,
            ],
        ];

        foreach ($medicines as $index => $medicineData) {
            $medicine = Medicine::create($medicineData);

            // Add to pharmacy inventory with varying quantities
            $quantity = match ($index % 4) {
                0 => rand(50, 200),  // High stock
                1 => rand(20, 49),   // Medium stock
                2 => rand(10, 19),   // Low stock
                3 => rand(5, 9),     // Very low stock (below reorder level)
            };

            PharmacyInventory::create([
                'pharmacy_id' => $pharmacy->id,
                'medicine_id' => $medicine->id,
                'quantity' => $quantity,
                'reorder_level' => 10,
                'unit_price' => rand(5, 50),
                'selling_price' => rand(10, 100),
                'batch_number' => 'BATCH-' . now()->format('Ym') . '-' . str_pad($index + 1, 3, '0', STR_PAD_LEFT),
                'expiry_date' => now()->addMonths(rand(6, 24)),
                'supplier' => 'شركة ' . ['المهندسين', 'الجمهورية', 'النصر', 'الحرية'][rand(0, 3)] . ' للأدوية',
            ]);
        }

        $this->command->info('✅ Demo pharmacy created: pharmacy@smart-medical.local / password');
        $this->command->info('✅ Created ' . count($medicines) . ' medicines with inventory');
    }
}

