<?php


namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Support\Facades\Auth;

abstract class ApiController extends Controller
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

    protected function getUser(): ?User
    {
        try {
            return $this->authenticate();
        } catch (AuthenticationException $e) {
            return null;
        }
    }
}
