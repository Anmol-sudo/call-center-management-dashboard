<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function summary(Request $request)
    {
        // TODO: Implement real reporting using a dedicated service.

        $period = $request->query('period', 'today');

        return response()->json([
            'period' => $period,
            'calls_per_employee' => [],
            'call_type_distribution' => [
                'incoming' => 0,
                'outgoing' => 0,
                'missed' => 0,
            ],
            'hourly_traffic' => [],
            'top_performers' => [],
        ]);
    }
}

