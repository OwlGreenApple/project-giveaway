<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('is_admin');
    }

    public function index()
    {
        $users = User::all()->where('is_admin',0);
        return view('admin.index',['users'=>$users,'no'=>1]);
    }

/* end controller */
}
