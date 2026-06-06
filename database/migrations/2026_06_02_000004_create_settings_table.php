<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('type')->default('string'); // string, boolean, integer, json
            $table->string('group')->default('general'); // general, registration, otp, features
            $table->string('description')->nullable();
            $table->timestamps();
        });

        // Default settings
        $settings = [
            // Registration settings
            ['key' => 'registration_open', 'value' => '1', 'type' => 'boolean', 'group' => 'registration', 'description' => 'حالة فتح التسجيل'],
            ['key' => 'registration_open_date', 'value' => null, 'type' => 'string', 'group' => 'registration', 'description' => 'تاريخ فتح التسجيل'],
            ['key' => 'registration_close_date', 'value' => null, 'type' => 'string', 'group' => 'registration', 'description' => 'تاريخ إغلاق التسجيل'],
            ['key' => 'registration_title', 'value' => 'بوابة القبول الإلكترونية', 'type' => 'string', 'group' => 'registration', 'description' => 'عنوان صفحة التسجيل'],
            ['key' => 'registration_description', 'value' => 'انضم إلى كورس تطوير مواقع وتطبيقات باستخدام الذكاء الاصطناعي', 'type' => 'string', 'group' => 'registration', 'description' => 'وصف صفحة التسجيل'],
            ['key' => 'rate_limit_per_ip', 'value' => '3', 'type' => 'integer', 'group' => 'registration', 'description' => 'حد التسجيل لكل IP بالساعة'],

            // OTP settings
            ['key' => 'otp_enabled', 'value' => '1', 'type' => 'boolean', 'group' => 'otp', 'description' => 'تفعيل نظام OTP'],
            ['key' => 'otp_api_key', 'value' => '', 'type' => 'string', 'group' => 'otp', 'description' => 'مفتاح API للـ OTP'],
            ['key' => 'otp_expiry_minutes', 'value' => '5', 'type' => 'integer', 'group' => 'otp', 'description' => 'مدة صلاحية الكود بالدقائق'],
            ['key' => 'otp_max_attempts', 'value' => '5', 'type' => 'integer', 'group' => 'otp', 'description' => 'عدد المحاولات المسموحة'],
            ['key' => 'otp_cooldown_seconds', 'value' => '25', 'type' => 'integer', 'group' => 'otp', 'description' => 'مدة الانتظار لإعادة الإرسال'],

            // Feature flags
            ['key' => 'feature_otp_verification', 'value' => '1', 'type' => 'boolean', 'group' => 'features', 'description' => 'تفعيل التحقق عبر OTP'],
            ['key' => 'feature_engineer_upload', 'value' => '1', 'type' => 'boolean', 'group' => 'features', 'description' => 'تفعيل رفع هوية النقابة'],
        ];

        foreach ($settings as $setting) {
            DB::table('settings')->insert(array_merge($setting, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
