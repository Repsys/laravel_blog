<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'email' => 'email|unique:users',
            'login' => 'required|unique:users',
            'password' => 'required',
            'confirm_password' => 'required|same:password',
        ]);

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
        $request->validate([
            'login' => 'required',
            'password' => 'required',
        ]);

        $credentials = $request->only('login', 'password');

        if (!Auth::attempt($credentials)) {
            $response = [
                'message' => 'Wrong login or password'
            ];
            return response()->json($response, 400);
        }

        $user = Auth::user();
        $response = [
            'message' => 'Successful authorization',
            'data'    => ['token' => $user->api_token],
        ];
        return response()->json($response, 201);
    }

}
