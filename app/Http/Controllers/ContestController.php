<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Storage;
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
use App\Http\Controllers\ApiController as API;

class ContestController extends Controller
{
    public function test_contestant()
    {
        $contestants = Contestants::take(3)->get()->shuffle();

        foreach($contestants as $contestant){
            echo $contestant->id."<br>";
        }

        //dd($contestants);

    }

    public function contest($link,$ref = null)
    {
        $event = Events::where('url_link',$link)->first();
        if(is_null($event))
        {
            return view('error404');
        }

        $images = array();
        $banners = Banners::where('event_id',$event->id)->select('url')->get();
        if(count($banners) > 0)
        {
            foreach($banners as $row):
                $images[] = Storage::disk('s3')->url($row->url); 
            endforeach;
        }
        
        $user = User::find($event->user_id);
        $total_contestant = Contestants::where([['contestants.event_id',$event->id],['events.user_id',$user->id]])
                            ->join('events','events.id','=','contestants.event_id')->get()->count();

        // dd($total_contestant);

        $data = [
            'event'=>$event,
            'banners'=>$images,
            'helpers'=>new Custom,
            'user'=>$user,
            'link'=>$link,
            'ref'=>$ref,
            'branding'=>$user->branding,
            'check_contestants'=>self::check_contestants_membership($user,$total_contestant)
        ];

        return view('contest',$data);
    }

    private static function check_contestants_membership($user,$total_contestant)
    {
        $ct = new Custom;
        $max_contestants = $ct->check_type($user->membership)['contestants'];
        if($total_contestant >= $max_contestants)
        {
            return false;
        }

        return true;
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

        //API
        $api = new API;
        $act_list_id = $ev->act_api_id; //activrespon
        $mlc_list_id = $ev->mlc_api_id; //mailchimp

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

            // save contestant data to api activrespon
            if($act_list_id > 0)
            {
                $dt = [
                    'api_name'=>$name,
                    'api_email'=>$email,
                    'api_phone'=>$phone,
                    'list_id'=>$act_list_id,
                ];
    
                $api->save_to_activrespon_lists($dt);
            }

            // save contestant data to mailchimp
            if($mlc_list_id !== '0')
            {
                $dta = [
                    'name'=>$name,
                    'email'=>$email,
                    'list_id'=>$mlc_list_id
                ];
    
                $api->add_mailchimp($dta);
            }
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

        $ev = Events::where('events.id',$ev_id)
                ->join('users','users.id','=','events.user_id')
                ->select("events.*","users.currency")
                ->first();

        $bonus = Bonus::where('event_id',$ev_id)->get();
        $banners = Banners::where('event_id',$ev->id)->select('url')->first();
        
        if(!is_null($banners))
        {
            $banners = $banners->toArray();
            $banners['url'] = Storage::disk('s3')->url($banners['url']);
        }
        else
        {
            $banners = array();
        }

        $contestants = Contestants::where([['event_id',$ev->id],['id',$id]])->first();
        
        $data = [
            'ev'=>$ev,
            'ct_id'=>$id,
            'bonus'=>$bonus,
            'banners'=>$banners,
            'ct'=>$contestants,
            'helper'=>new Custom,
        ];

        return view('task',$data);
    }

    // LOADING ALL TASK DATA
    public function taskdata(Request $request)
    {
        $id = strip_tags($request->ct_id);
        $ev_id = strip_tags($request->ev_id);

        $ev = Events::find($ev_id)->first();
        $bonus = Bonus::where('event_id',$ev_id)->get();

        $data = [
            'ev'=>$ev,
            'ct_id'=>$id,
            'bonus'=>$bonus,
            'helper'=>new Custom,
        ];
        return view('taskdata',$data);
    }

    // save entry and add user's entry after user doing task
    public function save_entry(Request $request)
    {
        // dd($request->all());
        $contestant_id = strip_tags($request->ct_id);
        $event_id = strip_tags($request->evid);
        $bonus_id = strip_tags($request->bid);
        $type = strip_tags($request->type);
        $bonus = null;
        $ret['success'] = 0;

        $etr = Entries::where([['event_id',$event_id],['contestant_id',$contestant_id],['type',$type],['bonus_id',$bonus_id]])
                        ->first();

        if($bonus_id == 0)
        {
            $prize = 3;
        }
        else
        {
            $bonus = Bonus::where([['event_id',$event_id],['id',$bonus_id]])->first();
        
            if(is_null($bonus))
            {
                return response()->json($ret);
            }

            $prize = $bonus->prize;
            $bonus_id = $bonus->id;
        }

        if(is_null($etr))
        {
            $et = new Entries;
            $et->event_id = $event_id;
            $et->contestant_id = $contestant_id;
            $et->bonus_id = $bonus_id;
            $et->type = $type;
            $et->prize = $prize;

            try{
                $et->save();
                $ret['success'] = 1;
            }
            catch(QueryException $e)
            {
                // echo $e->getMessage();
                $ret['success'] = 'err';
            }
        }

        // SHARE REDIRECT

        $ev = Events::find($event_id);
        $ev_link = $ev->url_link;

        $ct = Contestants::find($contestant_id);
        $ref_code = $ct->ref_code;

        if(env('APP_ENV') == 'local')
        {
            $share_url = env('APP_URL').'/c/'.$ev_link.'/'.$ref_code;
        }
        // else
        // {
        //     // $share_url = 
        // }

        if($type == 0 || $type == 3 || $type == 4 || $type == 5 || $type == 6)
        {
            // facebook like // youtube subscribe // podcast subscribe 
            // daily entries // click a link 
            $ret['url'] = $bonus->url;
        }
        if($type == 1)
        {
            // ig follow
            $ret['url'] = 'https://instagram.com/'.$bonus->url;
        }
        if($type == 2)
        {
            // twitter follow
            $ret['url'] = 'https://twitter.com/'.$bonus->url;
        }
        if($type == 7)
        {
            // watching youtube
            $ret['url'] = null;
        }
        if($type == 8)
        {
            // twitter
            $ret['url'] = "https://twitter.com/share?url=".$share_url."&hashtags=winner,giveaway";
        }
        elseif($type == 9)
        {
            // facebook share
            $ret['url'] = "https://www.facebook.com/sharer/sharer.php?u=".$share_url."";
        }
        elseif($type == 10)
        {
            // web whatsapp
            $ret['url'] = "whatsapp://send?text=".$share_url."";
        }
        elseif($type == 11)
        {
            // linkedin
            $ret['url'] = "https://www.linkedin.com/sharing/share-offsite/?url=".$share_url."";
        }
        elseif($type == 12)
        {
            // mail to
            $ret['url'] = "mailto:?subject=".$ev->title."&amp;body=".$share_url."";
        }

        return response()->json($ret);
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
