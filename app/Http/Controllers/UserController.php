<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{

    public function getUser($login)
    {
        return response()->json(['login' => $login]);
    }

    public function getProfile(Request $request)
    {
        return $request->user();
    }

    public function editProfile()
    {

    }

    public function blacklistUser()
    {

    }

    public function subscribeToUser()
    {

    }

}
