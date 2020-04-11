<?php


namespace App\Http\Controllers\Api\Auth;


use App\Http\Controllers\Api\ApiController;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LogoutController extends ApiController
{
    public function index(Request $request)
    {
        if (Auth::guard('web')->user()) {
            Auth::guard('web')->logout();
            $request->session()->invalidate();
        }

        /** @var User $user */
        if ($user = $this->authenticate()) {
            $token = hash('sha256', $request->bearerToken());

            $user->tokens()->where('token', '=', $token)->first()->delete();
        }
        return ['message'=> 'ok'];
    }
}
