<?php

namespace App\Http\Controllers;

use App\Models\BlacklistEntry;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class SubscriptionController extends Controller
{
    public function getSubscriptions(Request $request)
    {
        $user = $request->user();
        $subscriptions = $user->subscriptions()->get();
        return response()->json($subscriptions);
    }

    public function getSubscribers(Request $request)
    {
        $user = $request->user();
        $subscribers = $user->subscribers()->get();
        return response()->json($subscribers);
    }

    public function getBlacklist(Request $request)
    {
        $user = $request->user();
        $blacklist = $user->blacklist()->get();
        return response()->json($blacklist);
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

        $blacklistEntry = BlacklistEntry::query()
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

        $subscription = Subscription::query()
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
