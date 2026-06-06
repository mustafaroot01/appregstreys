<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('registrations', function (Blueprint $table) {
            $table->id();
            $table->string('order_id', 20)->unique()->comment('رقم الطلب الفريد مثل ITD-XXXXXX');
            $table->string('full_name')->comment('الاسم الثلاثي');
            $table->string('phone', 20)->comment('رقم الهاتف');
            $table->string('whatsapp_phone', 20)->nullable()->comment('رقم الواتساب');
            $table->string('address')->comment('عنوان السكن');
            $table->enum('gender', ['ذكر', 'أنثى'])->comment('الجنس');
            $table->string('study_level')->nullable()->comment('نوع الدراسة');
            $table->enum('is_employee', ['نعم', 'لا'])->default('لا')->comment('هل موظف');
            $table->boolean('is_engineer')->default(false)->comment('منتمي لنقابة المهندسين');
            $table->string('engineer_id_image')->nullable()->comment('صورة هوية النقابة');
            $table->enum('status', ['new', 'reviewing', 'accepted', 'rejected'])->default('new')->comment('حالة الطلب');
            $table->text('admin_notes')->nullable()->comment('ملاحظات الإدارة');
            $table->string('ip_address', 45)->nullable();
            $table->boolean('phone_verified')->default(false)->comment('تم التحقق من الهاتف');
            $table->timestamps();

            // Indexes
            $table->index('status');
            $table->index('phone');
            $table->index('is_engineer');
            $table->index('is_employee');
            $table->index('study_level');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('registrations');
    }
};
