<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PbxController extends Controller
{
    public function status(Request $request)
    {
        // TODO: Wire to real PBXStatusSnapshot / PbxClient service.

        return response()->json([
            'status' => 'disconnected',
            'pbx_version' => null,
            'uptime_seconds' => 0,
            'active_calls' => 0,
            'registered_extensions' => 0,
            'total_extensions' => 0,
            'max_concurrent_calls' => 0,
            'snapshot_taken_at' => now(),
        ]);
    }
}

