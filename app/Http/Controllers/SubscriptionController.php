<?php

namespace App\Http\Controllers;

use App\Models\BlacklistEntry;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class SubscriptionController extends Controller
{
    public function getSubscriptions(Request $request)
    {
        $user = $request->user();
        $subscriptions = $this->getSubscriptionsCache($user);
        return response()->json($subscriptions);
    }

    public function getSubscribers(Request $request)
    {
        $user = $request->user();
        $subscribers = $this->getSubscribersCache($user);
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

        $this->updateSubscriptionsCache($request->user());
        $this->updateSubscribersCache(User::query()->find($target_user_id));

        $response = [
            'message' => 'You have successfully subscribed to the user with id = '.$target_user_id
        ];
        return response()->json($response, 200);
    }

    protected function getSubscriptionsCache($user)
    {
        return json_decode(Redis::get('user_'.$user->id.'_subscriptions'));
    }

    protected function updateSubscriptionsCache($user)
    {
        $subscriptions = $user->subscriptions()->get();
        Redis::set('user_'.$user->id.'_subscriptions', $subscriptions);
        return $subscriptions;
    }

    protected function getSubscribersCache($user)
    {
        return json_decode(Redis::get('user_'.$user->id.'_subscribers'));
    }

    protected function updateSubscribersCache($user)
    {
        $subscribers = $user->subscribers()->get();
        Redis::set('user_'.$user->id.'_subscribers', $subscribers);
        return $subscribers;
    }

}
