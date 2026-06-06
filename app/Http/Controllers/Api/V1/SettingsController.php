<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\ActivityLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    /**
     * GET /api/v1/admin/settings
     * Get all settings grouped
     */
    public function index(): JsonResponse
    {
        $settings = Setting::all()->groupBy('group')->map(function ($items) {
            return $items->mapWithKeys(function ($item) {
                $value = match ($item->type) {
                    'boolean' => (bool) $item->value,
                    'integer' => (int) $item->value,
                    'json'    => json_decode($item->value, true),
                    default   => $item->value,
                };
                return [$item->key => [
                    'value'       => $value,
                    'type'        => $item->type,
                    'description' => $item->description,
                ]];
            });
        });

        return response()->json([
            'success' => true,
            'data'    => $settings,
        ]);
    }

    /**
     * PUT /api/v1/admin/settings
     * Update settings (batch)
     */
    public function update(Request $request): JsonResponse
    {
        $request->validate([
            'settings' => 'required|array',
            'settings.*.key'   => 'required|string|exists:settings,key',
            'settings.*.value' => 'present',
        ]);

        $updated = [];

        foreach ($request->settings as $item) {
            $setting = Setting::where('key', $item['key'])->first();
            if (!$setting) continue;

            $oldValue = $setting->value;
            $newValue = is_bool($item['value']) ? ($item['value'] ? '1' : '0') : (string) $item['value'];

            $setting->update(['value' => $newValue]);

            // Clear cache
            \Illuminate\Support\Facades\Cache::forget("setting_{$item['key']}");

            $updated[] = $item['key'];
        }

        ActivityLog::record(
            action: 'update_settings',
            description: 'تحديث إعدادات النظام: ' . implode(', ', $updated),
            targetType: 'setting',
        );

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث الإعدادات بنجاح',
            'data'    => ['updated_keys' => $updated],
        ]);
    }

    /**
     * POST /api/v1/admin/settings/open-now
     * Open registration immediately
     */
    public function openNow(): JsonResponse
    {
        Setting::setValue('registration_open', true);
        Setting::setValue('registration_open_date', now()->toIso8601String());

        ActivityLog::record('open_registration', 'فتح التسجيل فوراً');

        return response()->json([
            'success' => true,
            'message' => 'تم فتح التسجيل بنجاح',
        ]);
    }

    /**
     * POST /api/v1/admin/settings/close-now
     * Close registration immediately
     */
    public function closeNow(): JsonResponse
    {
        Setting::setValue('registration_open', false);
        Setting::setValue('registration_close_date', now()->toIso8601String());

        ActivityLog::record('close_registration', 'إغلاق التسجيل');

        return response()->json([
            'success' => true,
            'message' => 'تم إغلاق التسجيل بنجاح',
        ]);
    }
}
