<?php

namespace App\Http\Controllers;

use App\Models\BlacklistEntry;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class UserController extends Controller
{

    public function getUser(Request $request, $user_id)
    {
        $user = User::all()->firstWhere('id', $user_id);

        if (!$user) {
            $response = [
                'message' => 'User with id = '.$user_id.' not found'
            ];
            return response()->json($response, 404);
        }

        return response()->json($user);
    }

    public function getProfile(Request $request)
    {
        return $request->user();
    }

    public function editProfile()
    {

    }

    public function blacklistUser(Request $request)
    {
        $request->validate([
            'target_user_id' => [
                'required', 'integer', 'exists:users,id',
                Rule::notIn($request->user()->id)],
        ]);

        $target_user_id = $request->input('target_user_id');
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

    public function subscribeToUser(Request $request)
    {
        $request->validate([
            'target_user_id' => [
                'required', 'integer', 'exists:users,id',
                Rule::notIn($request->user()->id)],
        ]);

        $target_user_id = $request->input('target_user_id');
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
