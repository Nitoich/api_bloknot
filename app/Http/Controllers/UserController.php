<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function register(Request $request) {
        $validator = Validator::make($request->all(), [
            'login' => 'required|unique:users',
            'password' => 'required',
            'last_name' => 'required',
            'first_name' => 'required'
        ]);

        if($validator->fails()) {
            return response()->json([
                'error' => [
                    'code' => 400,
                    'message' => 'Validation error!',
                    'errors' => $validator->errors()
                ]
            ], 400);
        }

        $user = User::create([
            'login' => $request->login,
            'password' => Hash::make($request->password),
            'first_name' => $request->first_name,
            'last_name' => $request->last_name
        ]);

        if($user) {
            return response()->json([
                'token' => $user->generateToken()
            ], 201);
        }

        return response()->json([
            'error' => [
                'code' => 400,
                'message' => 'Neudalos sozdat usera!)'
            ]
        ]);
    }

    public function login(Request $request) {
        $validator = Validator::make($request->all(), [
            'login' => 'required',
            'password' => 'required'
        ]);

        if($validator->fails()) {
            return response()->json([
                'error' => [
                    'code' => 400,
                    'message' => 'Validation error!',
                    'errors' => $validator->errors()
                ]
            ], 400);
        }

        $user = User::where('login', $request->login)->first();

        // return $user->password == Hash::make($request->password);

        if($user) {
            if(Hash::check($request->password, $user->password)) {
                return response()->json([
                    'token' => $user->generateToken()
                ]);
            }
        }

        return response()->json([
            'error' => [
                'code' => 401,
                'message' => 'Unauthized'
            ]
        ]);
    }
}
