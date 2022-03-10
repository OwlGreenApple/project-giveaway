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
use App\Models\BroadcastContestant;
use App\Models\Entries;
use App\Models\Bonus;
use App\Models\Broadcast;
use App\Helpers\Custom;
use Carbon\Carbon;
use App\Http\Controllers\HomeController as Home;
use DB;

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
        $user = Auth::user();
        $helper = new Custom;
        $events = Events::where('user_id',$user->id)
                    ->get();
        return view('broadcast.create-broadcast',[
            'helper'=>$helper,
            'events'=>$events,
        ]);
    }

    // DISPLAY CONTESTANTS LIST
    public function display_contestants(Request $request)
    {
        $src = $request->contestant;

        if($src == null)
        {
            return;
        }

        $ct = Contestants::where([['events.user_id',Auth::id()],['contestants.c_name','LIKE','%'.$src.'%']])
             ->join('events','events.id','=','contestants.event_id')->select('contestants.id','contestants.c_name','contestants.wa_number')
             ->groupBy('contestants.id')->orderBy('contestants.c_name','ASC')->get();
        return view('broadcast.display-contestants',['data'=>$ct]);
    }

    public function edit_broadcast($id)
    {
        $user = Auth::user();
        $broadcast = Broadcast::find($id);
        if (is_null($broadcast)){
            return "Not found";
        }
        if ($broadcast->user_id <> $user->id) {
            return "not allowed";
        }

        $helper = new Custom;
        $events = Events::where('user_id',$user->id)
                    ->get();
        if (!is_null($broadcast)){
            return view('broadcast.create-broadcast',[
                'helper'=>$helper,
                'events'=>$events,
                'broadcast'=>$broadcast,
            ]);
        }
        else {
            return "Not found";
        }
    }

    public function save_broadcast(Request $request)
    {
        $user = Auth::user();
        $date_send = strip_tags($request->date_send);
        $title = strip_tags($request->title);
        $desc = strip_tags($request->desc);
        $timezone = strip_tags($request->timezone);
        $event_id = strip_tags($request->event);
        //validator

        //save to broadcast
        if ($request->mode==0) {
            $broadcast = new Broadcast;
        }
        else {
            $broadcast = Broadcast::find($request->mode);
            $broadcastContestant = BroadcastContestant::where('broadcast_id',$request->mode)->delete();
        }
        $broadcast->user_id = $user->id;
        $broadcast->event_id = $event_id;
        $broadcast->title = $title;
        //$broadcast->url = $request->url;
        $broadcast->message = $desc;
        $broadcast->date_send = Carbon::createFromFormat('Y-m-d H:i',$date_send);
        $broadcast->timezone = $timezone;
        $broadcast->save();

        // save to broadcast_contestant
        // klo $event_id = 0  -> all list(digroup by dengan no wa)
        if ($event_id==0) {
            $events = Events::where('user_id',$user->id)
            ->get();
            //https://stackoverflow.com/questions/30418452/trying-to-union-the-statements-using-loop-in-laravel/37324709
            $i=0;
            foreach($events as $event) {
                $q = DB::table('contestants')->where('event_id', '=', $event->id);
                if($i < 1){
                    $subcontestants = $q;
                }else{
                    $subcontestants->union($q);
                }
                $i++;
            }
            $contestants = [];
            if ($i>0) {
                $contestants = $subcontestants
                ->select('wa_number', 'id', 'created_at')
                ->groupBy('wa_number')
                ->orderBy('created_at', 'DESC')
                ->get();
            }
        }
        else {
            $contestants = Contestants::where('event_id',$event_id)->get();
        }
        foreach($contestants as $contestant) {
            $broadcastContestant = new BroadcastContestant;
            $broadcastContestant->broadcast_id = $broadcast->id;
            $broadcastContestant->contestant_id = $contestant->id;
            $broadcastContestant->save();
        }

        return response()->json([
            "success"=>1,
            "message"=>"success",
        ]);
    }

    public function list_broadcast_index()
    {
        $user = Auth::user();
        $broadcasts = Broadcast::where('user_id',$user->id)
                    ->get();
        return view('broadcast.list-broadcast',[
            'broadcasts'=>$broadcasts,
        ]);
    }

    public function delete_broadcast(Request $request)
    {
        $user = Auth::user();
        $broadcast = Broadcast::find($request->id);
        if ($broadcast->user_id <> $user->id) {
            return response()->json([
                "success"=>0,
                "message"=>"not allowed",
            ]);
        }

        $broadcast->delete();
        return response()->json([
            "success"=>1,
            "message"=>"Broadcast deleted",
        ]);
}

/* end class */
}
