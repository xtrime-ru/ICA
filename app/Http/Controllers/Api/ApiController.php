<?php


namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use App\User;
use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Support\Facades\Auth;

class ApiController extends Controller
{

    /**
     * @return Guard|StatefulGuard
     */
    protected function guard()
    {
        return Auth::guard('sanctum');
    }

    /**
     * @return User
     * @throws AuthenticationException
     */
    protected function authenticate(): User
    {
        /** @var User $user */
        $user = $this->guard()->user();

        if (!$user) {
            throw new AuthenticationException(trans('auth.failed'));
        }

        if (!$user->hasVerifiedEmail()) {
            throw new AuthenticationException(trans('auth.email'));
        }

        return $user;
    }
}
