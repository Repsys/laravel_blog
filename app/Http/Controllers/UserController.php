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

        $user = User::all()->firstWhere('id', $user_id);

        return response()->json($user);
    }

    public function getProfile(Request $request)
    {
        return $request->user();
    }

    public function editProfile(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'email' => 'email|unique:users',
            'login' => 'unique:users',
            'password' => '',
            'current_password' => 'required_with:email,login,password'
        ]);

        $current_password = $request->input('current_password');
        if (!empty($current_password) and !password_verify($current_password, $user->password)) {
            $response = [
                'message' => 'Wrong current password'
            ];
            return response()->json($response, 400);
        }

        $user->update($request->all());
        $user->save();

        $response = [
            'message' => 'Profile edited successfully',
            'data' => $user
        ];
        return response()->json($response, 200);
    }

    public function blacklistUser(Request $request, $user_id)
    {
        Validator::validate([
            'user_id' => $user_id
        ],[
            'user_id' => [
                'integer', 'exists:users,id',
                Rule::notIn($request->user()->id)],
        ]);

        $target_user_id = $user_id;
        $user_id = $request->user()->id;

        $blacklistEntry = BlacklistEntry::all()
            ->where('user_id', $user_id)
            ->firstWhere('target_user_id', $target_user_id);

        if ($blacklistEntry) {
            $blacklistEntry->delete();
            $response = [
                'message' => 'You have successfully unblacklisted the user with id = '.$target_user_id
            ];
            return response()->json($response, 200);
        }

        $blacklistEntry = new BlacklistEntry([
            'user_id' => $user_id,
            'target_user_id' => $target_user_id,
        ]);
        $blacklistEntry->save();

        $response = [
            'message' => 'You have successfully blacklisted the user with id = '.$target_user_id
        ];
        return response()->json($response, 200);
    }

    public function subscribeToUser(Request $request, $user_id)
    {
        Validator::validate([
            'user_id' => $user_id
        ],[
            'user_id' => [
                'integer', 'exists:users,id',
                Rule::notIn($request->user()->id)],
        ]);

        $target_user_id = $user_id;
        $user_id = $request->user()->id;

        $subscription = Subscription::all()
            ->where('user_id', $user_id)
            ->firstWhere('target_user_id', $target_user_id);

        if ($subscription) {
            $subscription->delete();
            $response = [
                'message' => 'You have successfully unsubscribed from the user with id = '.$target_user_id
            ];
            return response()->json($response, 200);
        }

        $subscription = new Subscription([
            'user_id' => $user_id,
            'target_user_id' => $target_user_id,
        ]);
        $subscription->save();

        $response = [
            'message' => 'You have successfully subscribed to the user with id = '.$target_user_id
        ];
        return response()->json($response, 200);
    }

}
