<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Registration;
use App\Models\ActivityLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminRegistrationController extends Controller
{
    /**
     * GET /api/v1/admin/registrations
     * List all registrations with filters
     */
    public function index(Request $request): JsonResponse
    {
        $query = Registration::query()
            ->filterByStatus($request->query('status'))
            ->filterByEngineer($request->query('engineer'))
            ->filterByEmployee($request->query('employee'))
            ->filterByStudyLevel($request->query('study_level'))
            ->filterByTrainingType($request->query('training_type'))
            ->search($request->query('search'))
            ->latest();

        // Pagination
        $perPage = min((int) $request->query('per_page', 25), 100);
        $registrations = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data'    => $registrations->items(),
            'meta'    => [
                'current_page' => $registrations->currentPage(),
                'last_page'    => $registrations->lastPage(),
                'per_page'     => $registrations->perPage(),
                'total'        => $registrations->total(),
            ],
        ]);
    }

    /**
     * GET /api/v1/admin/registrations/{id}
     * Show single registration details
     */
    public function show(int $id): JsonResponse
    {
        $registration = Registration::findOrFail($id);

        return response()->json([
            'success' => true,
            'data'    => $registration,
        ]);
    }

    /**
     * PUT /api/v1/admin/registrations/{id}
     * Update registration status/notes
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $registration = Registration::findOrFail($id);

        $request->validate([
            'status' => 'sometimes|in:new,reviewing,accepted,rejected',
            'admin_notes' => 'sometimes|nullable|string|max:1000',
        ]);

        $oldValues = [
            'status'      => $registration->status,
            'admin_notes' => $registration->admin_notes,
        ];

        $registration->update($request->only(['status', 'admin_notes']));

        ActivityLog::record(
            action: 'update_registration',
            description: "تحديث طلب {$registration->order_id}",
            targetType: 'registration',
            targetId: $registration->id,
            oldValues: $oldValues,
            newValues: $request->only(['status', 'admin_notes']),
        );

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث الطلب بنجاح',
            'data'    => $registration->fresh(),
        ]);
    }

    /**
     * DELETE /api/v1/admin/registrations/{id}
     * Delete a registration
     */
    public function destroy(int $id): JsonResponse
    {
        $registration = Registration::findOrFail($id);

        ActivityLog::record(
            action: 'delete_registration',
            description: "حذف طلب {$registration->order_id} - {$registration->full_name}",
            targetType: 'registration',
            targetId: $registration->id,
        );

        // Delete engineer ID image if exists
        if ($registration->engineer_id_image) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($registration->engineer_id_image);
        }

        $registration->delete();

        return response()->json([
            'success' => true,
            'message' => 'تم حذف الطلب بنجاح',
        ]);
    }

    /**
     * GET /api/v1/admin/registrations/export
     * Export registrations as CSV
     */
    public function export(Request $request)
    {
        $registrations = Registration::query()
            ->filterByStatus($request->query('status'))
            ->filterByEngineer($request->query('engineer'))
            ->filterByEmployee($request->query('employee'))
            ->filterByStudyLevel($request->query('study_level'))
            ->filterByTrainingType($request->query('training_type'))
            ->search($request->query('search'))
            ->latest()
            ->get();

        $csvFileName = 'registrations_' . now()->format('Y-m-d_His') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$csvFileName}\"",
        ];

        $callback = function () use ($registrations) {
            $file = fopen('php://output', 'w');

            // BOM for UTF-8
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // Header row
            fputcsv($file, [
                'رقم الطلب', 'الاسم', 'الهاتف', 'رقم الواتساب', 'البريد الإلكتروني', 'العنوان', 'الجنس',
                'نوع الدراسة', 'نوع التدريب', 'موظف', 'نقابة المهندسين', 'الحالة',
                'تاريخ التسجيل', 'ملاحظات الإدارة', 'نبذة عن المسجل',
            ]);

            $statusLabels = [
                'new'       => 'جديد',
                'reviewing' => 'قيد المراجعة',
                'accepted'  => 'مقبول',
                'rejected'  => 'مرفوض',
            ];

            foreach ($registrations as $reg) {
                fputcsv($file, [
                    $reg->order_id,
                    $reg->full_name,
                    $reg->phone,
                    $reg->whatsapp_phone ?? $reg->phone,
                    $reg->email,
                    $reg->address,
                    $reg->gender,
                    $reg->study_level,
                    $reg->training_type,
                    $reg->is_employee,
                    $reg->is_engineer ? 'نعم' : 'لا',
                    $statusLabels[$reg->status] ?? $reg->status,
                    $reg->created_at->format('Y-m-d H:i'),
                    $reg->admin_notes,
                    $reg->about_me,
                ]);
            }

            fclose($file);
        };

        ActivityLog::record('export', "تصدير بيانات ({$registrations->count()} سجل)");

        return response()->stream($callback, 200, $headers);
    }
}
