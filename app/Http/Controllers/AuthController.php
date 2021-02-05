<?php

namespace App\Http\Controllers;

use App\Models\User;
use http\Env\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'email',
            'login' => 'required',
            'password' => 'required',
            'confirm_password' => 'required|same:password',
        ]);

        if ($validator->fails()){
            $response = [
                'data' => $validator->errors(),
                'message' => 'Validation error.',
            ];
            return response()->json($response, 400);
        }

        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        $user = new User($input);
        $user->api_token = Str::random(60);
        $user->save();

        $response = [
            'data'    => ['token' => $user->api_token],
            'message' => 'Successful registration.',
        ];
        return response()->json($response, 201);
    }

    public function login(Request $request)
    {
        return response('login page');
    }

    public function unauthorized()
    {
        return response()->json([
            'message' => 'Unauthorized',
        ], 401);
    }

}
