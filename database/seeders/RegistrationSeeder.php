<?php

namespace Database\Seeders;

use App\Models\Registration;
use Illuminate\Database\Seeder;

class RegistrationSeeder extends Seeder
{
    public function run(): void
    {
        $registrations = [
            [
                'order_id'    => 'ITD-M1A2B3',
                'full_name'   => 'أحمد محمد علي',
                'phone'       => '07701234567',
                'whatsapp_phone' => '07701234567',
                'address'     => 'ديالى / بعقوبة',
                'gender'      => 'ذكر',
                'study_level' => 'بكلوريوس',
                'is_employee' => 'لا',
                'is_engineer' => true,
                'status'      => 'new',
                'phone_verified' => true,
            ],
            [
                'order_id'    => 'ITD-K4L5M6',
                'full_name'   => 'فاطمة أحمد حسن',
                'phone'       => '07809876543',
                'whatsapp_phone' => '07809876543',
                'address'     => 'ديالى / المقدادية',
                'gender'      => 'أنثى',
                'study_level' => 'دبلوم',
                'is_employee' => 'نعم',
                'is_engineer' => false,
                'status'      => 'accepted',
                'admin_notes' => 'مستندات كاملة',
                'phone_verified' => true,
            ],
            [
                'order_id'    => 'ITD-N7O8P9',
                'full_name'   => 'علي حسين علوان',
                'phone'       => '07712345678',
                'whatsapp_phone' => '07712345678',
                'address'     => 'ديالى / الخالص',
                'gender'      => 'ذكر',
                'study_level' => 'ماجستير',
                'is_employee' => 'نعم',
                'is_engineer' => true,
                'status'      => 'reviewing',
                'admin_notes' => 'انتظار تأكيد',
                'phone_verified' => true,
            ],
            [
                'order_id'    => 'ITD-Q1R2S3',
                'full_name'   => 'سارة خالد عباس',
                'phone'       => '07834567890',
                'whatsapp_phone' => '07834567890',
                'address'     => 'ديالى / بلدروز',
                'gender'      => 'أنثى',
                'study_level' => 'طالب اعدادي',
                'is_employee' => 'لا',
                'is_engineer' => false,
                'status'      => 'rejected',
                'admin_notes' => 'الاسم غير مكتمل',
                'phone_verified' => true,
            ],
            [
                'order_id'    => 'ITD-T4U5V6',
                'full_name'   => 'عمر محمود جعفر',
                'phone'       => '07723456789',
                'whatsapp_phone' => '07723456789',
                'address'     => 'ديالى / كنعان',
                'gender'      => 'ذكر',
                'study_level' => 'دكتوراه',
                'is_employee' => 'نعم',
                'is_engineer' => true,
                'status'      => 'new',
                'phone_verified' => true,
            ],
            [
                'order_id'    => 'ITD-W7X8Y9',
                'full_name'   => 'مريم جاسم صالح',
                'phone'       => '07845678901',
                'whatsapp_phone' => '07845678901',
                'address'     => 'ديالى / بعقوبة',
                'gender'      => 'أنثى',
                'study_level' => 'بكلوريوس',
                'is_employee' => 'لا',
                'is_engineer' => false,
                'status'      => 'accepted',
                'admin_notes' => 'تم إرسال إشعار القبول',
                'phone_verified' => true,
            ],
        ];

        foreach ($registrations as $data) {
            Registration::create($data);
        }
    }
}
