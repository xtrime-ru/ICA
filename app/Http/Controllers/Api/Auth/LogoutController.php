<?php


namespace App\Http\Controllers\Api\Auth;


use App\Http\Controllers\Api\ApiController;

class LogoutController extends ApiController
{
    public function index()
    {
        if ($this->authenticate()) {
            return ['message'=> 'ok'];
        }
        return ['message'=> 'Was not authenticated'];
    }
}
