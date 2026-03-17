<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function live(Request $request)
    {
        // TODO: Implement live dashboard aggregation using cached PBX + CallRecord data.

        return response()->json([
            'online_agents' => 0,
            'normal_calls' => 0,
            'missed_calls' => 0,
            'answered_calls' => 0,
            'employees' => [],
            'recent_calls' => [],
            'last_updated' => now(),
        ]);
    }
}

