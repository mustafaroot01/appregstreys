<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreRegistrationRequest;
use App\Models\Registration;
use App\Models\Setting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class RegistrationController extends Controller
{
    /**
     * POST /api/v1/register
     * Public: Submit a new registration
     */
    public function store(StoreRegistrationRequest $request): JsonResponse
    {
        // Check if registration is open
        if (!Setting::getValue('registration_open', true)) {
            return response()->json([
                'success' => false,
                'message' => 'التسجيل مغلق حالياً',
            ], 403);
        }

        // Rate limit by IP
        $maxPerIp = Setting::getValue('rate_limit_per_ip', 3);
        $recentFromIp = Registration::where('ip_address', $request->ip())
            ->where('created_at', '>=', now()->subHour())
            ->count();

        if ($recentFromIp >= $maxPerIp) {
            return response()->json([
                'success' => false,
                'message' => 'لقد تجاوزت الحد المسموح للتسجيل. حاول لاحقاً.',
            ], 429);
        }

        // Security Check: Ensure the phone number was actually verified via OTP recently
        if (Setting::getValue('feature_otp_verification', true)) {
            $phoneToCheck = $request->whatsapp_phone ?? $request->phone;
            $isVerified = \App\Models\OtpVerification::where('phone', $phoneToCheck)
                ->where('verified', true)
                ->where('updated_at', '>=', now()->subHours(2))
                ->exists();

            if (!$isVerified) {
                return response()->json([
                    'success' => false,
                    'message' => 'عملية مرفوضة: يجب التحقق من رقم الهاتف عبر الواتساب أولاً قبل التسجيل.',
                ], 403);
            }
        }

        // Handle engineer ID image upload
        $imagePath = null;
        if ($request->hasFile('engineer_id_image')) {
            $imagePath = $request->file('engineer_id_image')
                ->store('engineer-ids', 'public');
        }

        // Create registration
        $registration = Registration::create([
            'order_id'          => Registration::generateOrderId(),
            'full_name'         => $request->full_name,
            'phone'             => $request->phone,
            'whatsapp_phone'    => $request->whatsapp_phone ?? $request->phone,
            'email'             => $request->email,
            'address'           => $request->address,
            'gender'            => $request->gender,
            'study_level'       => $request->study_level,
            'training_type'     => $request->training_type,
            'about_me'          => $request->about_me,
            'is_employee'       => $request->is_employee,
            'is_engineer'       => $request->is_engineer === 'نعم',
            'engineer_id_image' => $imagePath,
            'status'            => 'new',
            'ip_address'        => $request->ip(),
            'phone_verified'    => Setting::getValue('feature_otp_verification', true) ? true : false,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'تم التسجيل بنجاح',
            'data'    => [
                'order_id'   => $registration->order_id,
                'created_at' => $registration->created_at->format('Y-m-d H:i'),
            ],
        ], 201);
    }

    /**
     * GET /api/v1/registration/status?order_id=XXX
     * Public: Check registration status
     */
    public function checkStatus(Request $request): JsonResponse
    {
        $request->validate([
            'order_id' => 'required|string',
        ]);

        $registration = Registration::where('order_id', $request->order_id)->first();

        if (!$registration) {
            return response()->json([
                'success' => false,
                'message' => 'رقم الطلب غير موجود',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => [
                'order_id' => $registration->order_id,
                'status'   => $registration->status,
                'date'     => $registration->created_at->format('Y-m-d H:i'),
            ],
        ]);
    }

    /**
     * GET /api/v1/registration/settings
     * Public: Get registration settings for frontend
     */
    public function publicSettings(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data'    => [
                'registration_open'        => Setting::getValue('registration_open', true),
                'registration_open_date'   => Setting::getValue('registration_open_date'),
                'registration_close_date'  => Setting::getValue('registration_close_date'),
                'registration_title'       => Setting::getValue('registration_title', 'بوابة القبول الإلكترونية'),
                'registration_description' => Setting::getValue('registration_description'),
                'otp_enabled'              => Setting::getValue('feature_otp_verification', true),
                'engineer_upload_enabled'  => Setting::getValue('feature_engineer_upload', true),
            ],
        ]);
    }
}
