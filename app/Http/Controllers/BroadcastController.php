<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\QueryException;
use App\Models\Events;
use App\Models\Banners;
use App\Models\User;
use App\Models\Contestants;
use App\Models\BroadcastContestant;
use App\Models\Entries;
use App\Models\Bonus;
use App\Models\Broadcast;
use App\Models\Messages;
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
        // $src = $request->contestant;
        $src = $request->contestant;
        $data = array();

        if($src == null)
        {
            return;
        }

        $ev = Events::where('user_id',Auth::id())->select('id')->get();

        if($ev->count() < 1)
        {
            return;
        }

        // PUT ALL EVENT ID TO ARRAY
        foreach($ev as $row)
        {
            $arrid[] = $row->id;
        }

        $ct = Contestants::where([['c_name','LIKE','%'.$src.'%']])->whereIn('event_id',$ev)
             ->select('id','c_email','c_name','wa_number')
             ->orderBy('c_name','ASC')->get();

        // filter to make unique value
        if($ct->count() > 0)
        {
            foreach($ct as $row):
                $data[$row->c_email] = [
                    'id'=>$row->id,
                    'name'=>$row->c_name,
                    'wa'=>$row->wa_number
                ];
            endforeach;
        }
        else
        {
            return;
        }

        return view('broadcast.display-contestants',['data'=>$data]);
    }

    public function edit_broadcast($id)
    {
        $user = Auth::user();
        $broadcast = Broadcast::find($id);
        if (is_null($broadcast)){
            return view('error404');
        }
        if ($broadcast->user_id <> $user->id) {
            return view('error404');
        }

        $helper = new Custom;
        $events = Events::where('user_id',$user->id)
                    ->get();

        if (!is_null($broadcast))
        {
            $ct = self::parsing_array($broadcast->ct_list);
            $cts = array();

            foreach($ct as $index=>$id):
                $member = Contestants::find($id);

                if(!is_null($member)):
                    $cts[$index] = [
                        'id'=>$member->id,
                        'name'=>$member->c_name,
                        'wa'=>$member->wa_number,
                    ];
                endif;
            endforeach;

            return view('broadcast.create-broadcast',[
                'helper'=>$helper,
                'events'=>$events,
                'broadcast'=>$broadcast,
                'ct'=>$cts,
                'obj'=>new Home
            ]);
        }
        else {
            return view('error404');
        }
    }

    public static function parsing_array($ct)
    {
        $arr=explode("|",$ct);
        array_pop($arr);
        return $arr;
    }


    public function save_broadcast(Request $request)
    {
        // dd($request->all());
        $user = Auth::user();
        $date_send = strip_tags($request->date_send);
        $title = strip_tags($request->title);
        $message = strip_tags($request->message);
        $timezone = strip_tags($request->timezone);
        $event_id = strip_tags($request->event);
        $ct_id = $request->ct_id;
        $ret = ["success"=>1];
        $cid = '';

        //save to new broadcast
        if ($request->mode == 0)
        {
            $broadcast = new Broadcast;
        }
        else {
            // TO PREVENT IF USER CHANGE BROADCAST ID
            $broadcast_check = Broadcast::where([['id',$request->mode],['user_id',Auth::id()]])->first();
            if(is_null($broadcast_check))
            {
                return response()->json(['success'=>0]);
            }
            $broadcast = Broadcast::find($request->mode);
            $ret = ["success"=>'edit','id'=>$request->mode];
        }

        // parse ct_id
        foreach($ct_id as $row):
            $cid .= strip_tags($row)."|";
        endforeach;

        // save broadcast image wa
        if($request->hasFile('media') == true)
        {
            // delete image in case of edit wa image
            if($broadcast->url !== null)
            {
                Storage::disk('s3')->delete($broadcast->url);
            }

            $save = new Home;
            $img_url = $save::save_wa_image($request,null);
            $broadcast->url = $img_url;
        }

        $broadcast->user_id = $user->id;
        $broadcast->ct_list = $cid;
        $broadcast->title = $title;
        $broadcast->message = $message;
        $broadcast->date_send = Carbon::createFromFormat('Y-m-d H:i',$date_send);
        $broadcast->timezone = $timezone;

        try
        {
            $broadcast->save();
        }
        catch(QueryException $e)
        {
            $ret = ["success"=>0];
        }

        return response()->json($ret);
    }

    // DISPLAY BROADCAST
    public function list_broadcast_index()
    {
        $user = Auth::user();
        $broadcasts = Broadcast::where('user_id',$user->id)->get();
        return view('broadcast.list-broadcast',[
            'broadcasts'=>$broadcasts,
        ]);
    }

    public function delete_broadcast(Request $request)
    {
        $user = Auth::user();
        $broadcast = Broadcast::find($request->id);
        if ($broadcast->user_id <> $user->id)
        {
            return response()->json(["success"=>0]);
        }

        // DELETE MESSAGE IF AVAILABLE
        $msg = Messages::where('bc_id',$broadcast->id);
        if($msg->get()->count() > 0)
        {
            $msg->delete();
        }

        // DELETE IMAGE IF AVAILABLE
        if($broadcast->url !== null)
        {
            Storage::disk('s3')->delete($broadcast->url);
        }

        try
        {
            $broadcast->delete();
            $ret = ["success"=>1];
        }
        catch(QueryException $e)
        {
            $ret = ["success"=>'db'];
        }

        return response()->json($ret);
}

/* end class */
}
