<?php


namespace App\Http\Controllers\Api\Auth;


use App\Http\Controllers\Api\ApiController;
use App\User;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

class LoginController extends ApiController
{
    use AuthenticatesUsers;

    public function index(Request $request) {
        return $this->login($request);
    }

    protected function authenticated(Request $request, User $user) {
        return [
            'user' => [
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'api_token' => $user->api_token
            ]
        ];
    }

    protected function sendLoginResponse(Request $request)
    {
        $this->clearLoginAttempts($request);

        return $this->authenticated($request, $this->guard()->user());
    }
}
