<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\OtpVerification;
use App\Models\Setting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OtpController extends Controller
{
    /**
     * POST /api/v1/otp/send
     * Public: Send OTP code to phone
     */
    public function send(Request $request): JsonResponse
    {
        // Check if OTP is enabled
        if (!Setting::getValue('feature_otp_verification', true)) {
            return response()->json([
                'success' => true,
                'message' => 'التحقق معطل حالياً، يمكنك المتابعة',
                'data'    => ['otp_disabled' => true],
            ]);
        }

        $request->validate([
            'phone' => ['required', 'string', 'regex:/^((\+?964)|0)?7[0-9]{9}$/'],
        ], [
            'phone.required' => 'رقم الهاتف مطلوب',
            'phone.regex'    => 'رقم الهاتف العراقي غير صحيح',
        ]);

        $phone = $request->phone;
        $maxAttempts = Setting::getValue('otp_max_attempts', 5);
        $cooldown    = Setting::getValue('otp_cooldown_seconds', 25);
        $expiry      = Setting::getValue('otp_expiry_minutes', 5);

        // Check rate limit: max OTP requests in 15 minutes
        $recentOtps = OtpVerification::where('phone', $phone)
            ->where('created_at', '>=', now()->subMinutes(15))
            ->count();

        if ($recentOtps >= $maxAttempts) {
            return response()->json([
                'success' => false,
                'message' => 'لقد تجاوزت الحد المسموح. انتظر 15 دقيقة.',
            ], 429);
        }

        // Check cooldown
        $lastOtp = OtpVerification::where('phone', $phone)
            ->latest()
            ->first();

        if ($lastOtp && $lastOtp->created_at->diffInSeconds(now()) < $cooldown) {
            $remaining = $cooldown - $lastOtp->created_at->diffInSeconds(now());
            return response()->json([
                'success' => false,
                'message' => "انتظر {$remaining} ثانية قبل إعادة الإرسال",
                'data'    => ['cooldown_remaining' => $remaining],
            ], 429);
        }

        // Ensure the SMS service is configured before creating any OTP record
        $apiKey = Setting::getValue('otp_api_key', '');

        if (empty($apiKey)) {
            return response()->json([
                'success' => false,
                'message' => 'خدمة الرسائل غير مهيأة حالياً. تواصل مع المشرف.',
            ], 500);
        }

        // Generate 6-digit code
        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // Save OTP
        $otp = OtpVerification::create([
            'phone'      => $phone,
            'code'       => $code,
            'expires_at' => now()->addMinutes($expiry),
            'ip_address' => $request->ip(),
        ]);

        // Normalize phone to +964...
        $normalizedPhone = preg_replace('/^0/', '', trim($phone));
        if (!str_starts_with($normalizedPhone, '+')) {
            $normalizedPhone = (!str_starts_with($normalizedPhone, '964')) ? '+964' . $normalizedPhone : '+' . $normalizedPhone;
        }

        try {
            $response = Http::timeout(15)->retry(2, 300)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $apiKey,
                    'Accept'        => 'application/json',
                ])
                ->post('https://api.otpiq.com/api/sms', [
                    'phoneNumber'      => ltrim($normalizedPhone, '+'),
                    'smsType'          => 'verification',
                    'verificationCode' => $code,
                    'provider'         => 'whatsapp',
                ]);

            if (!$response->successful()) {
                Log::error('OTPIQ sendOtp failed: ' . $response->status() . ' — ' . $response->body());
                $otp->delete(); // Clean up unused OTP record
                return response()->json([
                    'success' => false,
                    'message' => 'تعذر إرسال رمز التحقق، يرجى التأكد من رقم الهاتف أو المحاولة مجدداً.',
                ], 500);
            }
        } catch (\Exception $e) {
            Log::error('OtpService::sendOtp Exception: ' . $e->getMessage());
            $otp->delete(); // Clean up unused OTP record
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ في خدمة الرسائل.',
            ], 500);
        }

        // For now, log the code in development as fallback check
        if (config('app.debug')) {
            logger()->info("OTP Code for {$phone}: {$code}");
        }

        return response()->json([
            'success' => true,
            'message' => 'تم إرسال رمز التحقق',
            'data'    => [
                'expires_in' => $expiry * 60, // seconds
                'cooldown'   => $cooldown,
            ],
        ]);
    }

    /**
     * POST /api/v1/otp/verify
     * Public: Verify OTP code
     */
    public function verify(Request $request): JsonResponse
    {
        // If OTP is disabled, auto-verify
        if (!Setting::getValue('feature_otp_verification', true)) {
            return response()->json([
                'success' => true,
                'message' => 'تم التحقق بنجاح',
                'data'    => ['verified' => true],
            ]);
        }

        $request->validate([
            'phone' => ['required', 'string'],
            'code'  => ['required', 'string', 'size:6'],
        ], [
            'phone.required' => 'رقم الهاتف مطلوب',
            'code.required'  => 'رمز التحقق مطلوب',
            'code.size'      => 'رمز التحقق يجب أن يكون 6 أرقام',
        ]);

        $maxAttempts = Setting::getValue('otp_max_attempts', 5);

        $otp = OtpVerification::where('phone', $request->phone)
            ->where('verified', false)
            ->latest()
            ->first();

        if (!$otp) {
            return response()->json([
                'success' => false,
                'message' => 'لم يتم العثور على رمز تحقق لهذا الرقم',
            ], 404);
        }

        if ($otp->isExpired()) {
            return response()->json([
                'success' => false,
                'message' => 'رمز التحقق منتهي الصلاحية. أعد الإرسال.',
            ], 410);
        }

        // Increment attempts
        $otp->increment('attempts');

        if ($otp->attempts > $maxAttempts) {
            return response()->json([
                'success' => false,
                'message' => 'تجاوزت عدد المحاولات المسموحة',
            ], 429);
        }

        if (!$otp->isValid($request->code)) {
            return response()->json([
                'success' => false,
                'message' => 'رمز التحقق غير صحيح',
                'data'    => ['attempts_remaining' => max(0, $maxAttempts - $otp->attempts)],
            ], 422);
        }

        // Mark as verified
        $otp->update(['verified' => true]);

        return response()->json([
            'success' => true,
            'message' => 'تم التحقق بنجاح',
            'data'    => ['verified' => true],
        ]);
    }
}
