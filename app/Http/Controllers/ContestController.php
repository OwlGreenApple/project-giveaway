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
use App\Helpers\Custom;
use Carbon\Carbon;
use App\Http\Controllers\HomeController as Home;

class ContestController extends Controller
{
    public function contest($link,$ref = null)
    {
        $event = Events::where('url_link',$link)->first();
        if(is_null($event))
        {
            return view('error404');
        }

        if(env('APP_ENV') == 'local')
        {
            $url = asset('storage/app');
        }

        $images = array();
        $banners = Banners::where('event_id',$event->id)->select('url')->get()->toArray();
        if(count($banners) > 0)
        {
            foreach($banners as $row):
                $row['url'] = $url.'/'.$row['url'];
                $images[] = $row['url']; 
            endforeach;
        }

        $user = User::find($event->user_id);

        // dd($images);

        $data = [
            'event'=>$event,
            'banners'=>$images,
            'helpers'=>new Custom,
            'user'=>$user,
            'link'=>$link,
            'ref'=>$ref
        ];

        return view('contest',$data);
    }

    // save contestant
    public function save_contestant(Request $request)
    {
        // dd($request->all());
        $name = strip_tags($request->contestant);
        $email = strip_tags($request->email);
        $phone_code = strip_tags($request->pcode);
        $phone = $phone_code.strip_tags($request->phone);
        $link = strip_tags($request->link);
        $ref = strip_tags($request->ref);

        $ev = Events::where('url_link',$link)->first();

        if(is_null($ev))
        {
            $res['err'] = 2;
            return response()->json($res);
        }
 
        $ref_id = 0;
         // TO PREVENT IF USER ENTER WITH SAME EMAIL OR PHONE
         $check_identity = Contestants::where([['event_id',$ev->id],['wa_number',$phone]])
                        ->orWhere([['c_email',$email],['event_id',$ev->id]])
                        ->first();

        if($ref !== null)
        {
            $check = Contestants::where('ref_code',$ref)->first();
            if(!is_null($check) && is_null($check_identity))
            {
                $ref_id = $check->id;
                // referal system
                $refowner = Contestants::find($ref_id);
                $refowner->entries += 3;
                $refowner->referrals += 1;
                $refowner->save();
            }
        }

        if(is_null($check_identity))
        {
            $ct = new Contestants;
            $ct->event_id = $ev->id;
            $ct->ref_id = $ref_id;
            $ct->c_name = $name;
            $ct->c_email = $email;
            $ct->wa_number = $phone;
            $ct->ref_code = self::generate_ref_link();
            $ct->entries = 3;
            $ct->referrals = 0;
            $ct->ip = $_SERVER['REMOTE_ADDR'];
            $ct->date_enter = Carbon::now($ev->timezone);
        }
        else
        {
            $ct = Contestants::find($check_identity->id);
            $ct->c_name = $name;
            $ct->c_email = $email;
            $ct->wa_number = $phone;
        }
       
        try{
            $ct->save();
            if(is_null($check_identity))
            {
                $contestant_id = $ct->id;
            }
            else
            {
                $contestant_id = $check_identity->id;
            }

            // set cookie so that user can enter task page
            Cookie::queue(Cookie::make('ev_id', $ev->id, 1440*360));
            Cookie::queue(Cookie::make('ct_id', $contestant_id, 1440*360));

            $res['err'] = 0;
        }
        catch(QueryException $e)
        {
            //$e->getMessages()
            $res['err'] = 1;
        }

        return response()->json($res);
    }

    public function task()
    {
        // put cookie detection
        $id = Cookie::get('ct_id');// contestant id
        $ev_id = Cookie::get('ev_id');// event id

        if($id==null || $ev_id == null)
        {
            return view('error404');
        }

        $ev = Events::find($ev_id)->first();
        $bonus = Bonus::where('event_id',$ev_id)->get();
        
        $data = [
            'ev'=>$ev,
            'bonus'=>$bonus
        ];

        return view('task',$data);
    }

    // save entry and add user's entry after user doing task
    public function save_entry(Request $request)
    {
        $et = new Entries;
    }

    public static function generate_ref_link()
    {
        $home = new Home;
        $link = $home::generate_random();
        $ref = Contestants::where('ref_code',$link)->first();
        if(is_null($ref))
        {
            return $link;
        }
        else
        {
            return self::generate_ref_link();
        }
    }


/* end class */
}
