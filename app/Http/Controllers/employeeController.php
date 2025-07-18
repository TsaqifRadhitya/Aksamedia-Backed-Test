<?php

namespace App\Http\Controllers;

use App\Models\employee;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class employeeController extends Controller
{
    public function index(Request $request)
    {
        $name = $request->name;
        $division_id = $request->division_id;

        if ($name && $division_id) {
            $data = employee::with('division')->where('name', $name)->where('divisionId', $division_id)->paginate()->toArray();
        } else if ($name) {
            $data = employee::with('division')->where('name', $name)->paginate()->toArray();

        } else if ($division_id) {
            $data = employee::with('division')->where('divisionId', $division_id)->paginate()->toArray();

        } else {
            $data = employee::with('division')->paginate()->toArray();
        }

        $employess = collect($data['data'])->map(function ($employee) {
            $publicUrl = Storage::disk('s3')->url($employee['image']);
            $employee['image'] = $publicUrl;
            $position = $employee['position'];
            unset($employee['position']);
            return [...$employee, 'position' => $position];
        });

        unset($data['data']);

        return response()->json([
            'status' => 'success',
            'message' => '',
            'data' => [
                'employees' => $employess
            ],
            'pagination' => $data
        ]);
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'image' => ['required', 'file', 'mimes:jpg,jpeg,png,webp', 'max:1048'],
                'name' => ['required', 'string'],
                'phone' => ['required', 'string', 'unique:employees,phone'],
                'division' => ['required', 'string', 'uuid', 'exists:divisions,id'],
                'position' => ['required', 'string']
            ]);

            $imagePath = $request->file('image')->store('employee_profile', ['disk' => 's3', 'visibility' => 'public']);


            employee::create([
                'divisionId' => $request->division,
                'image' => $imagePath,
                'name' => $request->name,
                'phone' => $request->phone,
                'position' => $request->position
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'success create employee'
            ]);

        } catch (ValidationException $e) {

            return response()->json([
                "status" => "error",
                "message" => $e->getMessage()
            ], 400);

        }
    }

    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'image' => ['required', 'file', 'mimes:jpg,jpeg,png,webp', 'max:1048'],
                'name' => ['required', 'string'],
                'phone' => ['required', 'string', 'unique:employees,phone,' . $id . ',id'],
                'division' => ['required', 'string', 'uuid', 'exists:divisions,id'],
                'position' => ['required', 'string']
            ]);

            $employee = employee::find($id);

            if (!$employee) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'employee not found',
                ], 404);
            }

            Storage::disk('s3')->delete($employee->image);

            $imagePath = $request->file('image')->store('employee_profile', ['disk' => 's3', 'visibility' => 'public']);

            $employee->update([
                'divisionId' => $request->division,
                'image' => $imagePath,
                'name' => $request->name,
                'phone' => $request->phone,
                'position' => $request->position
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'success update employee'
            ]);

        } catch (ValidationException $e) {

            return response()->json([
                "status" => "error",
                "message" => $e->getMessage(),
            ], 400);

        }
    }

    public function destory($id)
    {
        $employee = employee::find($id);
        if (!$employee) {
            return response()->json([
                'status' => 'error',
                'message' => 'employee not found'
            ], 404);
        }

        Storage::disk('s3')->delete($employee->image);

        $employee->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'success delete employee'
        ]);
    }
}
