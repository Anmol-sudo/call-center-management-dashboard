<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        $query = Employee::query();

        if ($request->filled('name')) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }

        if ($request->filled('extension')) {
            $query->where('extension', $request->extension);
        }

        if ($request->filled('department')) {
            $query->where('department', $request->department);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        return response()->json($query->paginate());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'extension' => ['required', 'string', 'max:50', 'unique:employees,extension'],
            'department' => ['nullable', 'string', 'max:255'],
            'supervisor_id' => ['nullable', 'exists:supervisors,id'],
            'status' => ['required', 'string', 'max:50'],
        ]);

        $employee = Employee::create($validated);

        return response()->json($employee, 201);
    }

    public function update(Request $request, Employee $employee)
    {
        $validated = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'extension' => ['sometimes', 'string', 'max:50', 'unique:employees,extension,' . $employee->id],
            'department' => ['sometimes', 'nullable', 'string', 'max:255'],
            'supervisor_id' => ['sometimes', 'nullable', 'exists:supervisors,id'],
            'status' => ['sometimes', 'string', 'max:50'],
        ]);

        $employee->update($validated);

        return response()->json($employee);
    }

    public function destroy(Employee $employee)
    {
        $employee->delete();

        return response()->json([], 204);
    }
}

