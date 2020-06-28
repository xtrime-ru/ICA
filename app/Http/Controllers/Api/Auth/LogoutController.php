<?php


namespace App\Http\Controllers\Api\Auth;


use App\Http\Controllers\Api\ApiController;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\PersonalAccessToken;

class LogoutController extends ApiController
{
    public function index(Request $request)
    {
        if (Auth::guard('web')->user()) {
            Auth::guard('web')->logout();
            $request->session()->invalidate();
        }

        /** @var User $user */
        /** @var $token PersonalAccessToken */
        if (
            ($user = $this->authenticate())
            && ($token = $user->currentAccessToken())
        ) {
            $token->delete();
        }
        return ['message'=> 'ok'];
    }
}
