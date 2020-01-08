<?php


namespace App\Http\Controllers\Api\Auth;


use App\Http\Controllers\Api\ApiController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class LogoutController extends ApiController
{
    public function index(Request $request)
    {
        if (Auth::guard('web')->user()) {
            Auth::guard('web')->logout();
            $request->session()->invalidate();
        }
        return ['message'=> 'ok'];
    }
}
