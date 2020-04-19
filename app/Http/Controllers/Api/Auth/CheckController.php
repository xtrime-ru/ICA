<?php


namespace App\Http\Controllers\Api\Auth;


use App\Http\Controllers\Api\ApiController;

class CheckController extends ApiController
{

    public function index() {
        $this->authenticate();
        return ['check' => 'ok'];
    }

}
