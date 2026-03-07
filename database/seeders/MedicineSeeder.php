<?php

namespace Database\Seeders;

use App\Models\Medicine;
use Illuminate\Database\Seeder;

class MedicineSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $medicines = [
            ['name_ar' => 'باراسيتامول 500 مجم', 'name_en' => 'Paracetamol 500mg', 'active_ingredient' => 'Paracetamol', 'category' => 'مسكنات', 'form' => 'أقراص', 'description' => 'مسكن للألم وخافض للحرارة'],
            ['name_ar' => 'بروفين 400 مجم', 'name_en' => 'Ibuprofen 400mg', 'active_ingredient' => 'Ibuprofen', 'category' => 'مسكنات', 'form' => 'أقراص', 'description' => 'مضاد للالتهابات ومسكن للألم'],
            ['name_ar' => 'أوجمنتين 1 جم', 'name_en' => 'Augmentin 1g', 'active_ingredient' => 'Amoxicillin + Clavulanic Acid', 'category' => 'مضادات حيوية', 'form' => 'أقراص', 'description' => 'مضاد حيوي واسع المجال'],
            ['name_ar' => 'فلوموكس 1 جم', 'name_en' => 'Flumox 1g', 'active_ingredient' => 'Amoxicillin', 'category' => 'مضادات حيوية', 'form' => 'أقراص', 'description' => 'مضاد حيوي أموكسيسيلين'],
            ['name_ar' => 'كونجستال', 'name_en' => 'Congestal', 'active_ingredient' => 'Paracetamol + Chlorpheniramine', 'category' => 'أدوية البرد', 'form' => 'أقراص', 'description' => 'لعلاج أعراض البرد والإنفلونزا'],
            ['name_ar' => 'كومتركس', 'name_en' => 'Comtrex', 'active_ingredient' => 'Paracetamol + Pseudoephedrine', 'category' => 'أدوية البرد', 'form' => 'أقراص', 'description' => 'لعلاج أعراض البرد والجيوب الأنفية'],
            ['name_ar' => 'هستوب', 'name_en' => 'Histop', 'active_ingredient' => 'Loratadine', 'category' => 'مضادات الحساسية', 'form' => 'أقراص', 'description' => 'مضاد للهستامين للحساسية'],
            ['name_ar' => 'فنستيل نقط', 'name_en' => 'Fenistil Drops', 'active_ingredient' => 'Dimetindene', 'category' => 'مضادات الحساسية', 'form' => 'نقط', 'description' => 'مضاد للحساسية للأطفال'],
            ['name_ar' => 'بروفين شراب 100 مل', 'name_en' => 'Ibuprofen Syrup 100ml', 'active_ingredient' => 'Ibuprofen', 'category' => 'مسكنات', 'form' => 'شراب', 'description' => 'مسكن وخافض حرارة للأطفال'],
            ['name_ar' => 'سيفوتاكس 1 جم حقن', 'name_en' => 'Cefotax 1g Injection', 'active_ingredient' => 'Cefotaxime', 'category' => 'مضادات حيوية', 'form' => 'حقن', 'description' => 'مضاد حيوي من السيفالوسبورين'],
            ['name_ar' => 'فولتارين 50 مجم', 'name_en' => 'Voltaren 50mg', 'active_ingredient' => 'Diclofenac', 'category' => 'مسكنات', 'form' => 'أقراص', 'description' => 'مضاد للالتهابات ومسكن قوي'],
            ['name_ar' => 'أسبرين 100 مجم', 'name_en' => 'Aspirin 100mg', 'active_ingredient' => 'Aspirin', 'category' => 'أدوية القلب', 'form' => 'أقراص', 'description' => 'مسيل للدم ووقاية من الجلطات'],
            ['name_ar' => 'أوميبرازول 20 مجم', 'name_en' => 'Omeprazole 20mg', 'active_ingredient' => 'Omeprazole', 'category' => 'أدوية المعدة', 'form' => 'كبسولات', 'description' => 'لعلاج الحموضة وقرحة المعدة'],
            ['name_ar' => 'كونترولوك 40 مجم', 'name_en' => 'Controloc 40mg', 'active_ingredient' => 'Pantoprazole', 'category' => 'أدوية المعدة', 'form' => 'أقراص', 'description' => 'لعلاج الحموضة والارتجاع'],
            ['name_ar' => 'موتيليوم 10 مجم', 'name_en' => 'Motilium 10mg', 'active_ingredient' => 'Domperidone', 'category' => 'أدوية المعدة', 'form' => 'أقراص', 'description' => 'لعلاج الغثيان والقيء'],
            ['name_ar' => 'ستربتوكين', 'name_en' => 'Streptoquin', 'active_ingredient' => 'Streptomycin + Clioquinol', 'category' => 'أدوية المعدة', 'form' => 'أقراص', 'description' => 'لعلاج الإسهال والنزلات المعوية'],
            ['name_ar' => 'أنتينال شراب', 'name_en' => 'Antinal Syrup', 'active_ingredient' => 'Nifuroxazide', 'category' => 'أدوية المعدة', 'form' => 'شراب', 'description' => 'مطهر معوي للإسهال'],
            ['name_ar' => 'دياكس 5 مجم', 'name_en' => 'Diax 5mg', 'active_ingredient' => 'Diazepam', 'category' => 'مهدئات', 'form' => 'أقراص', 'description' => 'لعلاج القلق والتوتر العضلي'],
            ['name_ar' => 'أملور 5 مجم', 'name_en' => 'Amlor 5mg', 'active_ingredient' => 'Amlodipine', 'category' => 'أدوية الضغط', 'form' => 'أقراص', 'description' => 'لعلاج ضغط الدم المرتفع'],
            ['name_ar' => 'كونكور 5 مجم', 'name_en' => 'Concor 5mg', 'active_ingredient' => 'Bisoprolol', 'category' => 'أدوية الضغط', 'form' => 'أقراص', 'description' => 'لعلاج ضغط الدم والقلب'],
            ['name_ar' => 'جلوكوفاج 500 مجم', 'name_en' => 'Glucophage 500mg', 'active_ingredient' => 'Metformin', 'category' => 'أدوية السكري', 'form' => 'أقراص', 'description' => 'لعلاج السكري من النوع الثاني'],
            ['name_ar' => 'أماريل 2 مجم', 'name_en' => 'Amaryl 2mg', 'active_ingredient' => 'Glimepiride', 'category' => 'أدوية السكري', 'form' => 'أقراص', 'description' => 'لعلاج السكري من النوع الثاني'],
            ['name_ar' => 'كريستور 10 مجم', 'name_en' => 'Crestor 10mg', 'active_ingredient' => 'Rosuvastatin', 'category' => 'أدوية الكوليسترول', 'form' => 'أقراص', 'description' => 'لخفض الكوليسترول'],
            ['name_ar' => 'ليبيتور 20 مجم', 'name_en' => 'Lipitor 20mg', 'active_ingredient' => 'Atorvastatin', 'category' => 'أدوية الكوليسترول', 'form' => 'أقراص', 'description' => 'لخفض الكوليسترول والدهون'],
            ['name_ar' => 'كتافلام 50 مجم', 'name_en' => 'Cataflam 50mg', 'active_ingredient' => 'Diclofenac Potassium', 'category' => 'مسكنات', 'form' => 'أقراص', 'description' => 'مسكن سريع المفعول'],
            ['name_ar' => 'سيليبريكس 200 مجم', 'name_en' => 'Celebrex 200mg', 'active_ingredient' => 'Celecoxib', 'category' => 'مسكنات', 'form' => 'كبسولات', 'description' => 'مضاد للالتهابات انتقائي'],
            ['name_ar' => 'ليفوفلوكساسين 500 مجم', 'name_en' => 'Levofloxacin 500mg', 'active_ingredient' => 'Levofloxacin', 'category' => 'مضادات حيوية', 'form' => 'أقراص', 'description' => 'مضاد حيوي من الفلوروكينولون'],
            ['name_ar' => 'أزيثروميسين 500 مجم', 'name_en' => 'Azithromycin 500mg', 'active_ingredient' => 'Azithromycin', 'category' => 'مضادات حيوية', 'form' => 'أقراص', 'description' => 'مضاد حيوي واسع المجال'],
            ['name_ar' => 'فلاجيل 500 مجم', 'name_en' => 'Flagyl 500mg', 'active_ingredient' => 'Metronidazole', 'category' => 'مضادات حيوية', 'form' => 'أقراص', 'description' => 'مضاد حيوي للالتهابات اللاهوائية'],
            ['name_ar' => 'فنتولين بخاخ', 'name_en' => 'Ventolin Inhaler', 'active_ingredient' => 'Salbutamol', 'category' => 'أدوية الصدر', 'form' => 'بخاخ', 'description' => 'موسع للشعب الهوائية'],
            ['name_ar' => 'سيريتايد بخاخ', 'name_en' => 'Seretide Inhaler', 'active_ingredient' => 'Fluticasone + Salmeterol', 'category' => 'أدوية الصدر', 'form' => 'بخاخ', 'description' => 'لعلاج الربو المزمن'],
            ['name_ar' => 'بريدنيزولون 5 مجم', 'name_en' => 'Prednisolone 5mg', 'active_ingredient' => 'Prednisolone', 'category' => 'كورتيزون', 'form' => 'أقراص', 'description' => 'كورتيزون للالتهابات المزمنة'],
            ['name_ar' => 'فيتامين د 50000 وحدة', 'name_en' => 'Vitamin D 50000 IU', 'active_ingredient' => 'Cholecalciferol', 'category' => 'فيتامينات', 'form' => 'كبسولات', 'description' => 'لعلاج نقص فيتامين د', 'requires_prescription' => false],
            ['name_ar' => 'كالسيوم د 600 مجم', 'name_en' => 'Calcium D 600mg', 'active_ingredient' => 'Calcium + Vitamin D', 'category' => 'فيتامينات', 'form' => 'أقراص', 'description' => 'مكمل الكالسيوم وفيتامين د', 'requires_prescription' => false],
            ['name_ar' => 'فيروجلوبين كبسول', 'name_en' => 'Feroglobin Capsules', 'active_ingredient' => 'Iron + Vitamins', 'category' => 'فيتامينات', 'form' => 'كبسولات', 'description' => 'مكمل الحديد والفيتامينات', 'requires_prescription' => false],
            ['name_ar' => 'زنكترون شراب', 'name_en' => 'Zinctron Syrup', 'active_ingredient' => 'Zinc', 'category' => 'فيتامينات', 'form' => 'شراب', 'description' => 'مكمل الزنك للأطفال', 'requires_prescription' => false],
            ['name_ar' => 'ثيوتاسيد 600 مجم', 'name_en' => 'Thiotacid 600mg', 'active_ingredient' => 'Thioctic Acid', 'category' => 'أدوية الأعصاب', 'form' => 'أقراص', 'description' => 'لعلاج التهابات الأعصاب'],
            ['name_ar' => 'ميلجا أقراص', 'name_en' => 'Milga Tablets', 'active_ingredient' => 'Vitamin B Complex', 'category' => 'فيتامينات', 'form' => 'أقراص', 'description' => 'فيتامين ب المركب للأعصاب'],
            ['name_ar' => 'جابابنتين 300 مجم', 'name_en' => 'Gabapentin 300mg', 'active_ingredient' => 'Gabapentin', 'category' => 'أدوية الأعصاب', 'form' => 'كبسولات', 'description' => 'لعلاج آلام الأعصاب'],
            ['name_ar' => 'ليريكا 75 مجم', 'name_en' => 'Lyrica 75mg', 'active_ingredient' => 'Pregabalin', 'category' => 'أدوية الأعصاب', 'form' => 'كبسولات', 'description' => 'لعلاج آلام الأعصاب والصرع'],
        ];

        foreach ($medicines as $medicine) {
            Medicine::create($medicine);
        }

        $this->command->info('✅ Created '.count($medicines).' medicines');
    }
}
