<?php


namespace App\Http\Controllers\Api\Auth;


use App\Http\Controllers\Api\ApiController;
use App\Models\User;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class LoginController extends ApiController
{
    use AuthenticatesUsers;

    public function index(Request $request) {
        return $this->login($request);
    }

    protected function authenticated(Request $request, User $user) {
        $token = $user->createToken(User::getTokenName($request));
        return [
            'user' => [
                'role' => $user->role,
                'api_token' => $token->plainTextToken,
            ]
        ];
    }

    /**
     * Get the failed login response instance.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function sendFailedLoginResponse(Request $request)
    {
        throw ValidationException::withMessages([
            $this->username() => [trans('auth.failed')],
            'password' => [trans('auth.failed')],
        ]);
    }

    protected function sendLoginResponse(Request $request)
    {
        $this->clearLoginAttempts($request);

        return $this->authenticated($request, $this->guard()->user());
    }
}
