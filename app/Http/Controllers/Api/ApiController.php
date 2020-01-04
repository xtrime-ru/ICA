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
        return Auth::guard('api');
    }

    /**
     * @return User|null
     * @throws Exception
     */
    protected function authenticate(): ?User
    {
        /** @var User|null $user */
        $user = $this->guard()->user();

        if ($user && !$user->hasVerifiedEmail()) {
            throw new AuthenticationException(trans('auth.email'));
        }

        return $user;
    }
}
