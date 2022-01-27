<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Storage;
use App\Models\Banners;
use App\Models\Bonus;
use App\Models\User;
use App\Models\Events;
use App\Models\Contestants;
use App\Helpers\Custom;
use App\Models\Entries;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Aws\S3\Exception\S3Exception;

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

    public function dashboard()
    {
        $events = Events::where('events.user_id',Auth::id())
                ->leftJoin('contestants','contestants.event_id','=','events.id')
                ->select(DB::raw('COUNT(contestants.id) AS total_contestant, SUM(contestants.entries) AS total_entries, events.*'))
                ->groupBy('events.id')
                ->get();

        $data = ['data'=>$events];
        return view('home-table',$data);
    }

    // DUPLICATE EVENT
    public function duplicate_events(Request $request)
    {
        $ev_id = strip_tags($request->id);
        $ev = Events::where([['id',$ev_id],['user_id',Auth::id()]])->first();
        
        //check event
        if(is_null($ev))
        {
            return response()->json(['success'=>'err']);
        }

        //duplicate events
        $req = [
            'title'=>$ev->title,
            'start'=>$ev->start,
            'end'=>$ev->end,
            'award'=>$ev->award,
            'winner'=>$ev->winners,
            'timezone'=>$ev->timezone,
            'owner_name'=>$ev->owner,
            'owner_url'=>$ev->owner_url,
            'prize_name'=>$ev->prize_name,
            'prize_amount'=>$ev->prize_value,
            'youtube_url'=>$ev->youtube_banner,
            'desc'=>$ev->desc,
            'media_option'=>$ev->media,
            'unl_cam'=>$ev->unlimited,
            'tw'=>$ev->tw,
            'fb'=>$ev->fb,
            'wa'=>$ev->wa,
            'ln'=>$ev->ln,
            'mail'=>$ev->mail,
            'duplicate'=>1
        ];

        $reqt = new Request($req);
        $new_ev_id = $this->save_events($reqt);

        // duplicate banners
        $this->duplicate_banner($ev->id,$new_ev_id);

        //duplicate bonus 
        $bonuses = Bonus::where('event_id',$ev->id)->get();
        if($bonuses->count() > 0)
        {
            foreach($bonuses as $row):
                $bn = [
                    'event_id'=>$new_ev_id,
                    'title'=>$row->title,
                    'prize'=>$row->prize,
                    'type'=>$row->type,
                    'url'=>$row->url
                ];
                self::db_bonus($bn,"new");
            endforeach;
        }
    }

    private function duplicate_banner($ev_id,$new_event_id)
    {
        $duplicate = array();
        $banners = Banners::where('event_id',$ev_id)->select('url')->get();
        
        if($banners->count() > 0)
        {
            foreach($banners as $row):
                $duplicate[] = Storage::disk('s3')->url($row->url);
            endforeach;
        }

        $this->save_banner_image(null,$new_event_id,$duplicate);
    }

    public function del_event(Request $request)
    {
        $ev_id = strip_tags($request->id);
        $ev_check = Events::where([['id',$ev_id],['user_id',Auth::id()]])->first();

        if(is_null($ev_check))
        {
            return response()->json(['success'=>'err']);
        }

        try
        {
            $banners = Banners::where('event_id',$ev_id)->select('id')->get()->toArray();
            self::delete_banner(null,null,$banners);

            Bonus::where('event_id',$ev_id)->delete();
            Contestants::where('event_id',$ev_id)->delete();
            Entries::where('event_id',$ev_id)->delete();
            Events::find($ev_id)->delete();
            $res['success'] = 1;
        }
        catch(QueryException $e)
        {
            $res['success'] = 0;
        }

        return response()->json($res);
    }

    //  get contestant from events
    public function get_contestant($ev_id)
    {
        $events = Events::where([['id',$ev_id],['user_id',Auth::id()]])->first();

        if(is_null($events))
        {
            return view('error404');
        }

        $ct = Contestants::where('event_id',$ev_id)->get();
        $data = ['data'=>$ct,'ev'=>$events];
        return view('dashboard.contestant',$data);
    }

    public function create_giveaway()
    {
        $banners = $bonuses = array();
        $preloaded = null;
        $helper = new Custom;
        $user = Auth::user();
        $data = ['data'=>$banners,'preloaded'=>$preloaded, 'bonus'=>$bonuses, 'helper'=>$helper,'user'=>$user];
        return view('create',$data);
    }

    // Adding bonus entries title
    function fix_array($arr)
    {
        if($arr['type'] == 1)
        {
            $arr['name'] = Lang::get('custom.ig');
            $arr['col_name'] = Lang::get('custom.ig.col');
            $arr['mod'] = 'ig';
        }
        elseif($arr['type'] == 2)
        {
            $arr['name'] = Lang::get('custom.tw');
            $arr['col_name'] = Lang::get('custom.tw.col');
            $arr['mod'] = 'tw';
        }
        elseif($arr['type'] == 3)
        {
            $arr['name'] = Lang::get('custom.yt');
            $arr['col_name'] = Lang::get('custom.yt.col');
            $arr['mod'] = 'yt';
        }
        elseif($arr['type'] == 4)
        {
            $arr['name'] = Lang::get('custom.pt');
            $arr['col_name'] = Lang::get('custom.pt.col');
            $arr['mod'] = 'pt';
        }
        elseif($arr['type'] == 5)
        {
            $arr['name'] = Lang::get('custom.de');
            $arr['col_name'] = null;
            $arr['mod'] = 'de';
        }
        elseif($arr['type'] == 6)
        {
            $arr['name'] = Lang::get('custom.cl');
            $arr['col_name'] = Lang::get('custom.cl.col');
            $arr['mod'] = 'cl';
        }
        elseif($arr['type'] == 7)
        {
            $arr['name'] = Lang::get('custom.wyt');
            $arr['col_name'] = Lang::get('custom.wyt.col');
            $arr['mod'] = 'wyt';
        }
        else
        {
            $arr['name'] = Lang::get('custom.fb');
            $arr['col_name'] = Lang::get('custom.fb.col');
            $arr['mod'] = 'fb';
        }

        return $arr;
    }

    public function edit_event($id)
    {
        $event = Events::where([['events.id',$id],['users.id',Auth::id()]])->join('users','users.id','=','events.user_id')->first();
        
        if(is_null($event))
        {
            return view('error404');
        }
        
        $helper = new Custom;
        $data = $preloaded = null;
        $banners = Banners::where('event_id',$id)->get();

        $bonuses = Bonus::where('event_id',$id)->get()->toArray();
        $bonuses = array_map(array($this,'fix_array'),$bonuses);
    
        if($banners->count() > 0)
        {
            foreach($banners as $row)
            {
                $data[$row->id] = Storage::disk('s3')->url($row->url);
            }
            $preloaded = 'preloaded'; //keyname of jquery image-upload
        }

        //dd($data);
        $timezone = $event->timezone;
        $desc = $event->desc;
        $arr = [
            'data'=>$data,
            'preloaded'=>$preloaded, 
            'bonus'=>$bonuses, 
            'event'=>$event,
            'timezone'=>$timezone,
            'editor'=>$desc,
            'helper'=>$helper,
            'user'=>Auth::user()
        ];
        return view('create',$arr);
    }

    public function save_events(Request $request)
    {
        // dd($request->all());
        $req = $request->all();
        $edit = false;
        $helper = new Custom;
        $title = strip_tags($request->title);
        $start = strip_tags($request->start);
        $end = strip_tags($request->end);
        $award = strip_tags($request->award);
        $winner = strip_tags($request->winner);
        $timezone = strip_tags($request->timezone);
        $owner_name = strip_tags($request->owner_name);
        $owner_url = strip_tags($request->owner_url);
        $prize_name = strip_tags($request->prize_name);
        $prize_amount = strip_tags($helper::convert_amount($request->prize_amount));
        $youtube_url = strip_tags($request->youtube_url);
        $desc = $request->desc;
        $images = $request->file('images');
      
        $mo = self::determine_share($request->media_option);
        $unl = self::determine_share($request->unl_cam);
        $tw = self::determine_share($request->tw);
        $fb = self::determine_share($request->fb);
        $wa = self::determine_share($request->wa);
        $ln = self::determine_share($request->ln);
        $mail = self::determine_share($request->mail);

        if($request->edit == null)
        {
            $ev = new Events;
            $ev->user_id = Auth::id();
            $ev->url_link = self::generate_event_link();
        }
        else
        {
            $edit = true;
            $ev = Events::where([['events.id',$request->edit],['users.id',Auth::id()]])
                ->join('users','users.id','=','events.user_id')->first();

            if(is_null($ev))
            {
                $response['success'] = 'id';
                return response()->json($response);
            }
        }
        
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
            if($request->edit == null)
            {
                $event_id = $ev->id;
            }
            else
            {
                $event_id = $request->edit;
            }  
        }
        catch(QueryException $e)
        {
            // echo $e->getMessage();
            return response()->json(['success'=>0]);
        }

        if($request->duplicate == 1)
        {
            return $event_id;
        }

        /** BANNER IMAGES **/ 

        // DELETE BANNER
        $preload = $request->preloaded;

        if($preload !== null)
        {
            $lists = $request->list;
            $t_preload = count($preload);
            $t_lists = count($lists);

            if($t_preload !== $t_lists)
            {
                self::delete_banner($lists,$preload);
            }
        }
        
        // SAVE BANNER
        if(isset($images)): 
            $this->save_banner_image($request,$event_id);
        endif;

        /*** BONUS ENTRY ***/

        //DELETE BONUSES
        if(isset($req['entries']))
        {
            $t_entries = count($req['entries']);
            $t_compare = count($req['compare']);

            if($t_entries !== $t_compare)
            {
                $this->delete_bonuses($req['entries'],$req['compare']);            
            }
        }

        // FACEBOOK LIKE
        if(isset($req['new_text_fb']) || isset($req['edit_text_fb']))
        {
            $mod = 'fb';
            $type= 0;
            $this->call_bonus_entry($mod,$event_id,$type,$req);
        }

        // INSTAGRAM FOLLOW
        if(isset($req['new_text_ig']) || isset($req['edit_text_ig']))
        {
            $mod = 'ig';
            $type= 1;
            $this->call_bonus_entry($mod,$event_id,$type,$req);
        }
        
        // TWITTER FOLLOW
        if(isset($req['new_text_tw']) || isset($req['edit_text_tw']))
        {
            $mod = 'tw';
            $type= 2;
            $this->call_bonus_entry($mod,$event_id,$type,$req);
        }

        // YOUTUBE SUBSCRIBE
        if(isset($req['new_text_yt']) || isset($req['edit_text_yt']))
        {
            $mod = 'yt';
            $type= 3;
            $this->call_bonus_entry($mod,$event_id,$type,$req);
        }

        // PODCAST SUBSCRIBE
        if(isset($req['new_text_pt']) || isset($req['edit_text_fb']))
        {
            $mod = 'pt';
            $type= 4;
            $this->call_bonus_entry($mod,$event_id,$type,$req);
        }

        // DAILY ENTRY SUBSCRIBE
        if(isset($req['new_text_de']) || isset($req['edit_text_de']))
        {
            $mod = 'de';
            $type= 5;
            $this->call_bonus_entry($mod,$event_id,$type,$req);
        }

        // CLICK A LINK
        if(isset($req['new_text_cl']) || isset($req['edit_text_cl']))
        {
            $mod = 'cl';
            $type= 6;
            $this->call_bonus_entry($mod,$event_id,$type,$req);
        }

        // WATCHING YOUTUBE VIDEO
        if(isset($req['new_text_wyt']) || isset($req['edit_text_wyt']))
        {
            $mod = 'wyt';
            $type= 7;
            $this->call_bonus_entry($mod,$event_id,$type,$req);
        }

        return response()->json(['success'=>1,'id'=>$event_id]);
    }

    private static function determine_share($obj)
    {
        if($obj == null)
        {
            return 0;
        }
        
        if($obj > 0)
        {
            return 1;
        }
        else
        {
            return 0;
        }
    }

    // SAVE IMAGE BANNER ON EVENTS
    private function save_banner_image($request,$event_id,$duplicate = null)
    {
        /*
            Banner doesn't use edit because to change image, 
            user should delete and then reupload
        */

        if($duplicate == null)
        {
            $images = $request->file('images');
        }
        else
        {
            $images = $duplicate;
        }
        
        foreach($images as $index=>$file):
            $newfile = env('FOLDER_PATH').'/banner/'.Date('Y-m-d-h-i-s-').$index.".jpg";
            Storage::disk('s3')->put($newfile,file_get_contents($file), 'public');

            $banners = new Banners;
            $banners->event_id = $event_id;
            $banners->url = $newfile;
            $banners->save();
        endforeach;
    }

    // PASSING DATA FROM BONUS ENTRIES
    private function call_bonus_entry($mod,$event_id,$type,$req)
    {
        $id = array();
        // NEW BONUS ENTRY
        if(isset($req['new_text_'.$mod]))
        {
            $text = $req['new_text_'.$mod];
            $entries = $req['new_entries_'.$mod];

            ($type == 5)?$url = array(): $url = $req['new_url_'.$mod];
           
            $this->save_bonus_entry($text,$url,$entries,$event_id,$type,"new");
        }

        //EDIT BONUS ENTRY
        if(isset($req['edit_text_'.$mod]))
        {
            $text = $req['edit_text_'.$mod];
            $entries = $req['edit_entries_'.$mod];

            ($type == 5)?$url = array(): $url = $req['edit_url_'.$mod];

            $this->save_bonus_entry($text,$url,$entries,$event_id,$type,"edit");
        }
    }

    function mapping_data($title,$url,$prize,$index)
    {
        if($url == null)
        {
            $data = ['title'=>$title,'prize'=>$prize,'id'=>$index];
        }
        else
        {
            $data = [
                'title'=>$title,
                'url'=>$url,
                'prize'=>$prize,
                'id'=>$index
            ];
        }
       
        return $data;
    }

    // SAVE BONUS ENTRY
    private function save_bonus_entry($title, $url, $entries, $event_id, $type ,$cond)
    {
        $merge = array_map(array($this,'mapping_data'),$title, $url, $entries, array_keys($title));
        // dd($merge);
        
        foreach($merge as $key=>$row):
            $row['event_id'] = $event_id;
            $row['type'] = $type;

            if($type == 5)
            {
                $row['url'] = null;
            }
            self::db_bonus($row,$cond);
        endforeach;
    }

    // SAVE BONUS ENTRIES TO DATABASE
    public static function db_bonus(array $data,$cond)
    {
        if($cond == "new")
        {
            $bonus = new Bonus;
            $bonus->event_id = $data['event_id'];
        }
        else
        {
            $bonus = Bonus::find($data['id']);
        }
        
        $bonus->title = strip_tags($data['title']);
        $bonus->url = strip_tags($data['url']);
        $bonus->type = strip_tags($data['type']);
        $bonus->prize = strip_tags($data['prize']);

        try{
            $bonus->save();
        }
        catch(QueryException $e)
        {
            //
        }
    }

    public static function delete_banner($lists,$preloaded,$db = null)
    {
        $del = array();

        // preloaded from javascript banner
        if($preloaded == null)
        {
            $preloaded = array();
        }

        if($db == null)
        {
            $count_delete = array_diff($lists,$preloaded);
        }
        else
        {
            $count_delete = $db;
        }

        if(count($count_delete) > 0)
        {
            foreach($count_delete as $banner_id):
                $banners = Banners::find($banner_id)->first();
                $url_image = $banners->url;
                $del[] = $url_image;
            endforeach;

            try
            {
                Banners::whereIn('banners.id',$count_delete)
                        ->join('events','events.id','=','banners.event_id')
                        ->join('users','users.id','=','events.user_id')
                        ->where('users.id','=',Auth::id())
                        ->delete();
            }
            catch(QueryException $e)
            {
                // print($e->getMessage());
            }

            try{
                Storage::disk('s3')->delete($del);
            }
            catch(S3Exception $e)
            {
                // $e->getMessage();
            }
        }
    }

    private function delete_bonuses($compare,$entries)
    {
        
        $dels = array_diff($entries,$compare);

        if(count($dels) > 0)
        {
            try
            {
                Bonus::whereIn('bonuses.id',$dels)
                ->leftJoin('events','events.id','=','bonuses.event_id')
                ->leftJoin('users','users.id','=','events.user_id')
                ->where('users.id',Auth::id())
                ->delete();
            }
            catch(QueryException $e)
            {
                // print($e->getMessage());
            }
        }
    }

    // ACCOUNTS
    public function accounts()
    {
        // PROFILE
        $helper = new Custom;
        $user = User::find(Auth::id());

        $data = ['user'=>$user,'helper'=>$helper];
        return view('account',$data);
    }

    public function update_profile(Request $request)
    {
        $name = strip_tags($request->profile_name);
        $password = strip_tags($request->password);
        $currency = strip_tags($request->profile_currency);
        $lang = strip_tags($request->profile_lang);

        $update = [
            'name'=>$name,
            'currency'=>$currency,
            'lang'=>$lang,
        ];

        if($password !== null)
        {
            $update['password'] = Hash::make($password);
        }

        try{
            User::where('id',Auth::id())->update($update);
            $res['success'] = 1;
        }
        catch(QueryException $e)
        {
            //$e->getMessage()
            $res['success'] = 0;
        }

        return response()->json($res);
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

    public static function generate_random()
    {
        $permitted_chars = '0123456789abcdefghijklmnopqrstuvwxyz';
        return substr(str_shuffle($permitted_chars), 0, 9);
    }

/* end of class */
}
