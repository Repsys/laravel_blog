<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

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
        $token = $user->createToken("Token_name")->accessToken;

        $response = [
            'data'    => ['token' => $token],
            'message' => 'Successful registration.',
        ];
        return response()->json($response, 201);
    }

    public function login(Request $request)
    {

    }

}
