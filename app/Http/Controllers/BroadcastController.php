<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
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
        //validator

        //save to database
        $broadcast = new Broadcast;
        $broadcast->user_id = 0;
        $broadcast->event_id = 0;
        $broadcast->title = "";
        $broadcast->url = "";
        $broadcast->message = "";
        $broadcast->date_send = Carbon::now();
        $broadcast->save();

        return response()->json([
            "error"=>false,
            "message"=>"success",
        ]);
    }
/* end class */
}
