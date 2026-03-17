<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\CallRecord;
use App\Models\Employee;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function live(Request $request)
    {
        // Simple, non-cached aggregation over recent data for now.

        $onlineAgents = Employee::where('status', 'online')->count();
        $answeredCalls = CallRecord::where('status', 'answered')->count();
        $missedCalls = CallRecord::where('status', 'missed')->count();
        $normalCalls = CallRecord::count();

        $employees = Employee::with('supervisor')
            ->get()
            ->map(function (Employee $employee) {
                return [
                    'id' => $employee->id,
                    'name' => $employee->name,
                    'extension' => $employee->extension,
                    'department' => $employee->department,
                    'status' => $employee->status,
                    'supervisor' => optional($employee->supervisor)->id,
                ];
            });

        $recentCalls = CallRecord::with(['employee', 'phoneLine'])
            ->orderByDesc('started_at')
            ->limit(20)
            ->get()
            ->map(function (CallRecord $call) {
                return [
                    'id' => $call->id,
                    'employee' => optional($call->employee)->name,
                    'extension' => optional($call->employee)->extension,
                    'phone_line' => optional($call->phoneLine)->name,
                    'direction' => $call->direction,
                    'status' => $call->status,
                    'started_at' => $call->started_at,
                    'duration_seconds' => $call->duration_seconds,
                ];
            });

        return response()->json([
            'online_agents' => $onlineAgents,
            'normal_calls' => $normalCalls,
            'missed_calls' => $missedCalls,
            'answered_calls' => $answeredCalls,
            'employees' => $employees,
            'recent_calls' => $recentCalls,
            'last_updated' => now(),
        ]);
    }
}

