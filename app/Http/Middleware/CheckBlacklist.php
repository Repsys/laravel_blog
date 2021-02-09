<?php

namespace App\Http\Middleware;

use App\Models\BlacklistEntry;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;

class CheckBlacklist
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $user_id = $request->route('user_id');

        $blacklistEntry = BlacklistEntry::all()
            ->where('user_id', $user_id)
            ->firstWhere('target_user_id', $request->user()->id);

        if ($blacklistEntry) {
            $response = [
                'message' => 'User with id = '.$user_id.' blacklisted you'
            ];
            return response()->json($response, 403);
        }

        return $next($request);
    }
}
