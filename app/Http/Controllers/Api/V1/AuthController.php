<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\ActivityLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * POST /api/v1/admin/login
     */
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string|min:6',
        ], [
            'email.required'    => 'البريد الإلكتروني مطلوب',
            'email.email'       => 'البريد الإلكتروني غير صحيح',
            'password.required' => 'كلمة المرور مطلوبة',
        ]);

        $admin = Admin::where('email', $request->email)->first();

        if (!$admin || !Hash::check($request->password, $admin->password)) {
            return response()->json([
                'success' => false,
                'message' => 'بيانات الدخول غير صحيحة',
            ], 401);
        }

        if (!$admin->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'الحساب معطل، تواصل مع المسؤول',
            ], 403);
        }

        // Revoke old tokens
        $admin->tokens()->delete();

        // Create new token
        $token = $admin->createToken('admin-token', ['*'], now()->addHours(12));

        // Update last login
        $admin->update(['last_login_at' => now()]);

        // Log activity
        ActivityLog::record('login', "تسجيل دخول: {$admin->name}");

        return response()->json([
            'success' => true,
            'message' => 'تم تسجيل الدخول بنجاح',
            'data'    => [
                'admin' => [
                    'id'    => $admin->id,
                    'name'  => $admin->name,
                    'email' => $admin->email,
                    'role'  => $admin->role,
                ],
                'token' => $token->plainTextToken,
            ],
        ]);
    }

    /**
     * POST /api/v1/admin/logout
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        ActivityLog::record('logout', 'تسجيل خروج');

        return response()->json([
            'success' => true,
            'message' => 'تم تسجيل الخروج بنجاح',
        ]);
    }

    /**
     * GET /api/v1/admin/me
     */
    public function me(Request $request): JsonResponse
    {
        $admin = $request->user();

        return response()->json([
            'success' => true,
            'data'    => [
                'id'            => $admin->id,
                'name'          => $admin->name,
                'email'         => $admin->email,
                'role'          => $admin->role,
                'last_login_at' => $admin->last_login_at?->format('Y-m-d H:i'),
            ],
        ]);
    }
}
