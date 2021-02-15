<?php

namespace App\Http\Controllers;

use App\Models\BlacklistEntry;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class UserController extends Controller
{

    public function getUser(Request $request, $user_id)
    {
        Validator::validate([
            'user_id' => $user_id
        ],[
            'user_id' => ['integer', 'exists:users,id'],
        ]);

        $user = User::query()->find($user_id);

        return response()->json($user);
    }

    public function getProfile(Request $request)
    {
        return response()->json($request->user());
    }

    public function editProfile(Request $request)
    {
        $request->validate([
            'email' => 'email|unique:users',
            'login' => 'max:50|unique:users',
            'password' => 'max:100',
            'current_password' => 'required_with:email,login,password|max:100'
        ]);

        $input = $request->all();
        $user = $request->user();

        $current_password = $input['current_password'];
        if (!empty($current_password) and !password_verify($current_password, $user->password)) {
            $response = [
                'message' => 'Wrong current password'
            ];
            return response()->json($response, 400);
        }

        $input['password'] = bcrypt($input['password']);
        $user->update($input);
        $user->save();

        $response = [
            'message' => 'Profile edited successfully',
            'data' => $user
        ];
        return response()->json($response, 200);
    }

}
