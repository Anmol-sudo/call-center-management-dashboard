<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\CallRecord;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class ReportController extends Controller
{
    public function summary(Request $request)
    {
        $period = $request->query('period', 'today');

        $now = now();
        $start = match ($period) {
            'week' => $now->copy()->startOfWeek(),
            'month' => $now->copy()->startOfMonth(),
            default => $now->copy()->startOfDay(),
        };

        $callsQuery = CallRecord::whereBetween('started_at', [$start, $now]);

        // Calls per employee
        $callsPerEmployee = $callsQuery
            ->clone()
            ->selectRaw('employee_id, count(*) as total, sum(case when status = "missed" then 1 else 0 end) as missed, sum(case when status = "answered" then 1 else 0 end) as answered')
            ->groupBy('employee_id')
            ->get()
            ->map(function ($row) {
                $employee = Employee::find($row->employee_id);
                return [
                    'employee_id' => $row->employee_id,
                    'name' => optional($employee)->name,
                    'extension' => optional($employee)->extension,
                    'answered' => (int) $row->answered,
                    'missed' => (int) $row->missed,
                    'total' => (int) $row->total,
                    'response_rate' => $row->total > 0
                        ? round($row->answered * 100 / $row->total, 1)
                        : 0,
                ];
            })
            ->values();

        // Call type distribution
        $callTypeDistribution = [
            'incoming' => (int) $callsQuery->clone()->where('direction', 'incoming')->count(),
            'outgoing' => (int) $callsQuery->clone()->where('direction', 'outgoing')->count(),
            'missed' => (int) $callsQuery->clone()->where('status', 'missed')->count(),
        ];

        // Hourly traffic (simple buckets)
        $hourlyTraffic = $callsQuery
            ->clone()
            ->get()
            ->groupBy(function (CallRecord $call) {
                return Carbon::parse($call->started_at)->format('H:00');
            })
            ->map(function ($group, $hour) {
                return [
                    'hour' => $hour,
                    'count' => $group->count(),
                ];
            })
            ->values()
            ->sortBy('hour')
            ->values();

        // Top performers (sort by response rate then answered)
        $topPerformers = $callsPerEmployee
            ->sortByDesc('response_rate')
            ->sortByDesc('answered')
            ->values()
            ->take(10);

        return response()->json([
            'period' => $period,
            'calls_per_employee' => $callsPerEmployee,
            'call_type_distribution' => $callTypeDistribution,
            'hourly_traffic' => $hourlyTraffic,
            'top_performers' => $topPerformers,
        ]);
    }
}

