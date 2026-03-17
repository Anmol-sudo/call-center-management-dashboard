<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Supervisor;
use Illuminate\Http\Request;

class SupervisorController extends Controller
{
    public function index()
    {
        return response()->json(Supervisor::with('employees')->paginate());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'extension' => ['nullable', 'string', 'max:50'],
        ]);

        $supervisor = Supervisor::create($validated);

        return response()->json($supervisor, 201);
    }

    public function update(Request $request, Supervisor $supervisor)
    {
        $validated = $request->validate([
            'user_id' => ['sometimes', 'required', 'exists:users,id'],
            'extension' => ['sometimes', 'nullable', 'string', 'max:50'],
        ]);

        $supervisor->update($validated);

        return response()->json($supervisor);
    }

    public function destroy(Supervisor $supervisor)
    {
        $supervisor->delete();

        return response()->json([], 204);
    }

    public function employees(Supervisor $supervisor)
    {
        return response()->json($supervisor->employees()->paginate());
    }
}

