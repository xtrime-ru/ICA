<?php


namespace App\Http\Controllers\Api\Auth;


use App\Http\Controllers\Api\ApiController;

class MeController extends ApiController
{
    public function index() {
        $user = $this->authenticate();
        return [
            'user' => [
                'role' => $user->role,
                'api_token' => 'token',
            ]
        ];
    }

}
