<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Database\QueryException;
use App\Models\Events;
use App\Models\Banners;
use App\Models\User;
use App\Models\Contestants;
use App\Models\Entries;
use App\Models\Bonus;
use App\Models\Broadcast;
use App\Helpers\Custom;
use Carbon\Carbon;
use App\Http\Controllers\HomeController as Home;

class BroadcastController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function list_broadcast()
    {
        return view('broadcast.list-broadcast');
    }

    public function create_broadcast()
    {
        $helper = new Custom;
        return view('broadcast.create-broadcast',[
            'helper'=>$helper
        ]);
    }

    public function save_broadcast(Request $request)
    {
        $user = Auth::user();
        $date_send = strip_tags($request->date_send);
        $title = strip_tags($request->title);
        $desc = strip_tags($request->desc);
        $timezone = strip_tags($request->timezone);
        //validator

        //save to database
        $broadcast = new Broadcast;
        $broadcast->user_id = $user->id;
        $broadcast->event_id = 0;
        $broadcast->title = $title;
        //$broadcast->url = $request->url;
        $broadcast->message = $desc;
        $broadcast->date_send = Carbon::createFromFormat('Y-m-d H:i',$date_send);
        $broadcast->timezone = $timezone;
        $broadcast->save();

        return response()->json([
            "success"=>1,
            "message"=>"success",
        ]);
    }
/* end class */
}
