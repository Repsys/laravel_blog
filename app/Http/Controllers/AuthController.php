<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'email|unique:users',
            'login' => 'required|unique:users',
            'password' => 'required',
            'confirm_password' => 'required|same:password',
        ]);

        if ($validator->fails()){
            $response = [
                'message' => 'Validation error',
                'data' => $validator->errors(),
            ];
            return response()->json($response, 400);
        }

        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        $user = new User($input);
        $user->api_token = Str::random(60);
        $user->save();

        $response = [
            'message' => 'Successful registration',
            'data'    => ['token' => $user->api_token],
        ];
        return response()->json($response, 201);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'login' => 'required',
            'password' => 'required',
        ]);

        if ($validator->fails()){
            $response = [
                'message' => 'Validation error',
                'data' => $validator->errors(),
            ];
            return response()->json($response, 400);
        }

        $user = User::all()
            ->firstWhere('login', $request->input('login'));

        if (!$user or !password_verify( $request->input('password'), $user->password)) {
            $response = [
                'message' => 'Wrong login or password'
            ];
            return response()->json($response, 400);
        }

        $response = [
            'message' => 'Successful authorization',
            'data'    => ['token' => $user->api_token],
        ];
        return response()->json($response, 201);
    }

}
