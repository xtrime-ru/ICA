<?php


namespace App\Http\Controllers;


class AppController extends Controller
{
    /**
     * Show the application.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('layouts.app');
    }
}
