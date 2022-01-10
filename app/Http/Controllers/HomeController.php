<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('home');
    }

    public function create_giveaway()
    {
        return view('create');
    }

    public function accounts()
    {
        return view('account');
    }

    public function contact()
    {
        return view('contact');
    }

    public function connect_wa()
    {
        return view('connect');
    }

    public function contest()
    {
        return view('contest');
    }

/* end of class */
}
