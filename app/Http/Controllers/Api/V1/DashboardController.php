<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Registration;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    /**
     * GET /api/v1/admin/dashboard
     * Dashboard statistics
     */
    public function index(): JsonResponse
    {
        $totalRegistrations = Registration::count();
        $newRegistrations   = Registration::where('status', 'new')->count();
        $acceptedCount      = Registration::where('status', 'accepted')->count();
        $rejectedCount      = Registration::where('status', 'rejected')->count();
        $reviewingCount     = Registration::where('status', 'reviewing')->count();

        $engineerCount    = Registration::where('is_engineer', true)->count();
        $nonEngineerCount = Registration::where('is_engineer', false)->count();

        $employeeYes = Registration::where('is_employee', 'نعم')->count();
        $employeeNo  = Registration::where('is_employee', 'لا')->count();

        $todayCount = Registration::whereDate('created_at', today())->count();

        // Study level breakdown
        $studyLevels = Registration::selectRaw('study_level, COUNT(*) as count')
            ->groupBy('study_level')
            ->pluck('count', 'study_level')
            ->toArray();

        // Daily registrations for last 7 days
        $dailyStats = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $dailyStats[] = [
                'date'  => $date->format('Y-m-d'),
                'label' => $date->format('m/d'),
                'count' => Registration::whereDate('created_at', $date)->count(),
            ];
        }

        // Gender breakdown
        $maleCount   = Registration::where('gender', 'ذكر')->count();
        $femaleCount = Registration::where('gender', 'أنثى')->count();

        // Recent registrations
        $recentRegistrations = Registration::latest()->take(5)->get();

        return response()->json([
            'success' => true,
            'data'    => [
                'total'     => $totalRegistrations,
                'new'       => $newRegistrations,
                'accepted'  => $acceptedCount,
                'rejected'  => $rejectedCount,
                'reviewing' => $reviewingCount,
                'today'     => $todayCount,

                'engineers'      => $engineerCount,
                'non_engineers'  => $nonEngineerCount,
                'employee_yes'   => $employeeYes,
                'employee_no'    => $employeeNo,

                'male'   => $maleCount,
                'female' => $femaleCount,

                'study_levels' => $studyLevels,
                'daily_stats'  => $dailyStats,
                
                'recent_registrations' => $recentRegistrations,
            ],
        ]);
    }
}
