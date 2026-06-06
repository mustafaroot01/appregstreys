<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class StoreRegistrationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Public endpoint
    }

    public function rules(): array
    {
        return [
            'full_name'      => ['required', 'string', 'min:5', 'max:100', 'regex:/^[\p{Arabic}\s]+$/u'],
            'phone'          => ['required', 'string', 'regex:/^((\+?964)|0)?7[0-9]{9}$/'],
            'whatsapp_phone' => ['nullable', 'string', 'regex:/^((\+?964)|0)?7[0-9]{9}$/'],
            'email'          => ['nullable', 'email', 'max:255'],
            'address'        => ['required', 'string', 'min:3', 'max:200'],
            'gender'         => ['required', 'in:ذكر,أنثى'],
            'study_level'    => ['required', 'string', 'in:طالب اعدادي,دبلوم,بكلوريوس,ماجستير,دكتوراه,غير ذلك'],
            'training_type'  => ['required', 'string', 'in:حضوري,اون لاين'],
            'about_me'       => ['nullable', 'string', 'max:1000'],
            'is_employee'    => ['required', 'in:نعم,لا'],
            'is_engineer'    => ['required', 'in:نعم,لا'],
            'engineer_id_image' => ['nullable', 'required_if:is_engineer,نعم', 'image', 'mimes:jpeg,jpg,png,webp', 'max:10240'],
        ];
    }

    public function messages(): array
    {
        return [
            'full_name.required'        => 'الاسم الثلاثي مطلوب',
            'full_name.regex'           => 'الاسم يجب أن يحتوي على حروف عربية فقط',
            'full_name.min'             => 'الاسم قصير جداً',
            'phone.required'            => 'رقم الهاتف مطلوب',
            'phone.regex'               => 'رقم الهاتف العراقي غير صحيح',
            'email.email'               => 'صيغة البريد الإلكتروني غير صحيحة',
            'address.required'          => 'عنوان السكن مطلوب',
            'gender.required'           => 'الجنس مطلوب',
            'gender.in'                 => 'الجنس غير صحيح',
            'study_level.required'      => 'نوع الدراسة مطلوب',
            'study_level.in'            => 'نوع الدراسة غير صحيح',
            'training_type.required'    => 'يرجى تحديد نوع التدريب',
            'training_type.in'          => 'نوع التدريب غير صالح',
            'is_employee.required'      => 'حقل الوظيفة مطلوب',
            'is_engineer.required'      => 'حقل النقابة مطلوب',
            'engineer_id_image.required_if' => 'صورة هوية النقابة مطلوبة عند اختيار منتمي',
            'engineer_id_image.image'   => 'الملف يجب أن يكون صورة',
            'engineer_id_image.max'     => 'حجم الصورة يجب أن لا يتجاوز 10 ميغابايت',
        ];
    }
}
