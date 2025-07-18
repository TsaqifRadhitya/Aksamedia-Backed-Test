<?php

namespace App\Http\Controllers;

use App\Models\User;
use Auth;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class authController extends Controller
{
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'username' => ['required'],
                'password' => ['required']
            ]);

            $authCheck = Auth::attempt($validated);

            if (!$authCheck) {
                return response()->json([
                    "status" => "error",
                    "message" => "Invalid credentials"
                ], 401);
            }

            $user = User::firstWhere('username', $validated['username']);

            $token = $user->createToken('auth');

            return response()->json([
                "status" => "success",
                "message" => "login success",
                [
                    'data' => [
                        'token' => $token->plainTextToken,
                        'admin' => $user
                    ]
                ]
            ]);


        } catch (ValidationException $e) {
            return response()->json([
                "status" => "error",
                "message" => $e->getMessage()
            ], 400);
        }
    }

    public function destroy(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'logout success'
        ]);
    }

}
