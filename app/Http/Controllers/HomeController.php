<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use App\Models\Banners;
use App\Models\Bonus;
use App\Models\User;
use App\Models\Events;
use Illuminate\Database\QueryException;

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
        $banners = $bonuses = array();
        $preloaded = null;
        return view('create',['data'=>$banners,'preloaded'=>$preloaded, 'bonus'=>$bonuses]);
    }

    public function edit_event()
    {
        $banners = Banners::where('event_id',1)->get();
        $bonuses = Bonus::where('event_id',1)->get()->toArray();
        $data = $preloaded = null;


        if($banners->count() > 0)
        {
            foreach($banners as $row)
            {
                $data[$row->id] = asset('storage/app/'.$row->url);
            }
            $preloaded = 'preloaded'; //keyname of jquery image-upload
        }

        //dd($data);
        return view('create',['data'=>$data,'preloaded'=>$preloaded, 'bonus'=>$bonuses]);
    }

    public static function convert_amount($amount)
    {
        $amount = str_replace(",","",$amount);
        return (int)$amount;
    }

    public function save_events(Request $request)
    {
        // dd($request->all());
        $req = $request->all();
        $title = strip_tags($request->title);
        $start = strip_tags($request->start);
        $end = strip_tags($request->end);
        $award = strip_tags($request->award);
        $winner = strip_tags($request->winner);
        $timezone = strip_tags($request->timezone);
        $owner_name = strip_tags($request->owner_name);
        $owner_url = strip_tags($request->owner_url);
        $prize_name = strip_tags($request->prize_name);
        $prize_amount = strip_tags(self::convert_amount($request->prize_amount));
        $youtube_url = strip_tags($request->youtube_url);
        $desc = $request->desc;
        $images = $request->file('images');
        $mo = $unl = $tw = $fb = $wa = $ln = $mail = 0;

        (isset($request->media_option))? $mo = 1 : false;
        (isset($request->unl_cam))? $unl = 1 : false;
        (isset($request->tw))? $tw = $request->tw : false;
        (isset($request->fb))? $fb = $request->fb : false;
        (isset($request->wa))? $wa = $request->wa : false;
        (isset($request->ln))? $ln = $request->ln : false;
        (isset($request->mail))? $mail = $request->mail : false;

        $ev = new Events;
        $ev->user_id = Auth::id();
        $ev->url_link = self::generate_event_link();
        $ev->title = $title;
        $ev->desc = $desc;
        $ev->start = $start;
        $ev->end = $end;
        $ev->award = $award;
        $ev->unlimited = $unl;
        $ev->winners = $winner;
        $ev->owner = $owner_name;
        $ev->owner_url = $owner_url;
        $ev->prize_name = $prize_name;
        $ev->prize_value = $prize_amount;
        $ev->media = $mo;
        $ev->youtube_banner = $youtube_url;
        $ev->tw = $tw;
        $ev->fb = $fb;
        $ev->wa = $wa;
        $ev->ln = $ln;
        $ev->mail = $mail;
        $ev->timezone = $timezone;

        try{
            $ev->save();
            $event_id = $ev->id;
        }
        catch(QueryException $e)
        {
            // echo $e->getMessage();
            return response()->json(['error'=>1]);
        }
        
        // BANNER IMAGES
        if(isset($images)): 
            $this->save_banner_image($request,$event_id);
        endif;

        /*** BONUS ENTRY ***/

        // FACEBOOK LIKE
        if(isset($req['new_text_fb']))
        {
            $mod = 'fb';
            $type= 0;
            $this->call_bonus_entry($mod,$event_id,$type,$req);
        }

        // INSTAGRAM FOLLOW
        if(isset($req['new_text_ig']))
        {
            $mod = 'ig';
            $type= 1;
            $this->call_bonus_entry($mod,$event_id,$type,$req);
        }
        
        // TWITTER FOLLOW
        if(isset($req['new_text_tw']))
        {
            $mod = 'tw';
            $type= 2;
            $this->call_bonus_entry($mod,$event_id,$type,$req);
        }

        // YOUTUBE SUBSCRIBE
        if(isset($req['new_text_yt']))
        {
            $mod = 'yt';
            $type= 3;
            $this->call_bonus_entry($mod,$event_id,$type,$req);
        }

        // PODCAST SUBSCRIBE
        if(isset($req['new_text_pt']))
        {
            $mod = 'pt';
            $type= 4;
            $this->call_bonus_entry($mod,$event_id,$type,$req);
        }

        // DAILY ENTRY SUBSCRIBE
        if(isset($req['new_text_de']))
        {
            $mod = 'de';
            $type= 5;
            $this->call_bonus_entry($mod,$event_id,$type,$req);
        }

        // CLICK A LINK
        if(isset($req['new_text_cl']))
        {
            $mod = 'cl';
            $type= 6;
            $this->call_bonus_entry($mod,$event_id,$type,$req);
        }

        // WATCHING YOUTUBE VIDEO
        if(isset($req['new_text_wyt']))
        {
            $mod = 'wyt';
            $type= 7;
            $this->call_bonus_entry($mod,$event_id,$type,$req);
        }
    }

    // SAVE IMAGE BANNER ON EVENTS
    private function save_banner_image($request,$event_id)
    {
        $images = $request->file('images');
        $preload = $request->preloaded;
        $lists = $request->list;

        if($lists !== null)
        {
            self::delete_banner($lists,$preload);
        }

        foreach($images as $index=>$file):
            $newfile = 'banner/'.Date('Y-m-d-h-i-s-').$index.".jpg";
            Storage::disk('local')->put($newfile,file_get_contents($file));
            $banners = new Banners;
            $banners->event_id = $event_id;
            $banners->url = $newfile;
            $banners->save();
        endforeach;
        
        //Storage::disk('s3')->delete($filename);
        //Storage::disk('s3')->put($dir."/".$filename,$imageUpload, 'public');
    }

    // PASSING DATA FROM BONUS ENTRIES
    private function call_bonus_entry($mod,$event_id,$type,$req)
    {
        if($type == 5)
        {
            $this->save_bonus_entry($req['new_text_'.$mod], array(), $req['new_entries_'.$mod],$event_id,$type);
        }
        else
        {
            $this->save_bonus_entry($req['new_text_'.$mod], $req['new_url_'.$mod], $req['new_entries_'.$mod],$event_id,$type);
        }
    }

    function mapping_data($title,$url,$prize)
    {
        if($url == null)
        {
            $data = ['title'=>$title,'prize'=>$prize];
        }
        else
        {
            $data = [
                'title'=>$title,
                'url'=>$url,
                'prize'=>$prize
            ];
        }
       
        return $data;
    }

    // SAVE BONUS ENTRY
    private function save_bonus_entry($title, $url, $entries, $event_id, $type)
    {
        $merge = array_map(array($this,'mapping_data'),$title, $url, $entries);
        
        // dd($merge);
        foreach($merge as $key=>$row):
            $row['event_id'] = $event_id;
            $row['type'] = $type;
            if($type == 5)
            {
                $row['url'] = null;
            }
            self::db_bonus($row);
        endforeach;
    }

    // SAVE BONUS ENTRIES TO DATABASE
    public static function db_bonus(array $data)
    {
        $bonus = new Bonus;
        $bonus->event_id = $data['event_id'];
        $bonus->title = $data['title'];
        $bonus->url = $data['url'];
        $bonus->type = $data['type'];
        $bonus->prize = $data['prize'];

        try{
            $bonus->save();
        }
        catch(QueryException $e)
        {
            //
        }
    }

    public static function delete_banner($lists,$preloaded)
    {
        $del = array();

        // preloaded from javascript banner
        if($preloaded == null)
        {
            $preloaded = array();
        }

        $count_delete = array_diff($lists,$preloaded);

        if(count($count_delete) > 0)
        {
            foreach($count_delete as $banner_id):
                $banners = Banners::find($banner_id);
                $url_image = $banners->url;
                $del[] = $url_image;
            endforeach;

            try
            {
                Banners::whereIn('id',$count_delete)->delete();
            }
            catch(QueryException $e)
            {
                // print($e->getMessage());
            }

            Storage::disk('local')->delete($del);
        }
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

    public static function generate_event_link()
    {
        $link = self::generate_random();
        $ev = Events::where('url_link',$link)->first();
        if(is_null($ev))
        {
            return $link;
        }
        else
        {
            return self::generate_event_link();
        }
    }

    private static function generate_random()
    {
        $permitted_chars = '0123456789abcdefghijklmnopqrstuvwxyz';
        return substr(str_shuffle($permitted_chars), 0, 9);
    }

/* end of class */
}
