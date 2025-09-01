<?php

namespace Database\Seeders;

use App\Models\Guardian;
use App\Models\Student;
use Illuminate\Database\Seeder;

class GuardianSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // إنشاء أولياء أمور تجريبيين
        $guardians = [
            [
                'name' => 'أحمد محمد العلي',
                'phone' => '0501234567',
                'email' => 'ahmed.ali@example.com',
                'national_id' => '1234567890',
                'address' => 'الرياض، حي النخيل',
                'job' => 'مهندس',
                'relationship' => 'father',
                'is_active' => true,
                'notes' => 'ولي أمر متعاون ومهتم بتعليم أولاده',
            ],
            [
                'name' => 'فاطمة سعد الأحمد',
                'phone' => '0509876543',
                'email' => 'fatima.ahmed@example.com',
                'national_id' => '0987654321',
                'address' => 'جدة، حي الصفا',
                'job' => 'معلمة',
                'relationship' => 'mother',
                'is_active' => true,
                'notes' => 'أم مهتمة بالتحفيظ',
            ],
            [
                'name' => 'عبدالله خالد المطيري',
                'phone' => '0555555555',
                'email' => 'abdullah.mutairi@example.com',
                'national_id' => '5555555555',
                'address' => 'الدمام، حي الشاطئ',
                'job' => 'طبيب',
                'relationship' => 'father',
                'is_active' => true,
                'notes' => 'يرغب في متابعة تقدم أولاده',
            ],
            [
                'name' => 'نورا عبدالرحمن السالم',
                'phone' => '0544444444',
                'email' => 'nora.salem@example.com',
                'national_id' => '4444444444',
                'address' => 'مكة المكرمة، حي العزيزية',
                'job' => 'ربة منزل',
                'relationship' => 'mother',
                'is_active' => true,
                'notes' => 'متابعة ممتازة لأولادها',
            ],
            [
                'name' => 'محمد عبدالعزيز القحطاني',
                'phone' => '0533333333',
                'email' => 'mohammed.qahtani@example.com',
                'national_id' => '3333333333',
                'address' => 'المدينة المنورة، حي قباء',
                'job' => 'موظف حكومي',
                'relationship' => 'father',
                'is_active' => true,
                'notes' => 'حريص على التعليم الديني',
            ],
        ];

        foreach ($guardians as $guardianData) {
            // إنشاء كود الدخول من آخر 4 أرقام من الهاتف
            $guardianData['access_code'] = Guardian::generateAccessCode($guardianData['phone']);
            
            $guardian = Guardian::create($guardianData);
            
            // ربط أولياء الأمور بالطلاب (إذا كانت هناك طلاب)
            $students = Student::inRandomOrder()->limit(rand(1, 3))->get();
            
            foreach ($students as $student) {
                // تجنب الربط المكرر
                if (!$guardian->students()->where('student_id', $student->id)->exists()) {
                    $guardian->students()->attach($student->id, [
                        'relationship_type' => $guardian->relationship,
                        'is_primary' => rand(0, 1) == 1, // عشوائي
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }

        $this->command->info('تم إنشاء ' . count($guardians) . ' ولي أمر تجريبي بنجاح!');
        
        // عرض معلومات تسجيل الدخول
        $this->command->info('معلومات تسجيل الدخول:');
        foreach (Guardian::all() as $guardian) {
            $this->command->info("الاسم: {$guardian->name} | الهاتف: {$guardian->phone} | الكود: {$guardian->access_code}");
        }
    }
}
