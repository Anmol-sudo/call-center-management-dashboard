<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\PhoneLine;
use Illuminate\Http\Request;

class PhoneLineController extends Controller
{
    public function index()
    {
        return response()->json(PhoneLine::with('employees')->paginate());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'number' => ['required', 'string', 'max:50'],
            'type' => ['required', 'in:incoming,outgoing,both'],
            'status' => ['required', 'in:active,inactive'],
        ]);

        $phoneLine = PhoneLine::create($validated);

        return response()->json($phoneLine, 201);
    }

    public function update(Request $request, PhoneLine $phoneLine)
    {
        $validated = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'number' => ['sometimes', 'string', 'max:50'],
            'type' => ['sometimes', 'in:incoming,outgoing,both'],
            'status' => ['sometimes', 'in:active,inactive'],
        ]);

        $phoneLine->update($validated);

        return response()->json($phoneLine);
    }

    public function destroy(PhoneLine $phoneLine)
    {
        $phoneLine->delete();

        return response()->json([], 204);
    }
}

