<?php

namespace App\Http\Controllers;

use App\Models\division;
use Illuminate\Http\Request;

class divisionController extends Controller
{
    public function index(Request $request)
    {
        $name = $request->input('name');
        if ($name) {
            $data = division::where('name', $name)->paginate()->toArray();
        } else {
            $data = division::paginate()->toArray();
        }
        $dataDivision = $data['data'];
        unset($data['data']);
        return response()->json([
            'status' => 'success',
            'message' => '',
            'data' => [
                'divisions' => $dataDivision
            ],
            'pagination' => $data
        ]);
    }
}
