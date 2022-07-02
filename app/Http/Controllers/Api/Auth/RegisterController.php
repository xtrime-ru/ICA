<?php


namespace App\Http\Controllers\Api\Auth;


use App\Http\Controllers\Api\ApiController;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterController extends ApiController
{

    public function index(Request $request)
    {
        Validator::make(
            $request->all(),
            [
                'name' => ['required', 'string', 'max:100'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
                'password' => ['required', 'string', 'min:6', 'max:32', 'confirmed'],
            ]
        )->validate();

        event(new Registered($user = $this->create($request->all())));

        return [
            'role' => $user->role,
            'api_token' => $user->createToken(User::getTokenName($request)),
        ];
    }

    protected function create(array $data): User
    {
        return User::create(
            [
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
            ]
        );
    }
}
