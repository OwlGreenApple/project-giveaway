<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Config;
use App\Http\Middleware\CheckEvents;
use App\Helpers\Custom;
use App\Models\Banners;
use App\Models\Bonus;
use App\Models\User;
use App\Models\Events;
use App\Models\Contestants;
use App\Models\Entries;
use App\Models\Orders;
use App\Models\Messages;
use App\Models\Redeem;
use App\Mail\ContactEmail;
use App\Exports\ContestantExport;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Aws\S3\Exception\S3Exception;
use App\Http\Controllers\ApiController as API;
use Carbon\Carbon;

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

    //DISPLAY CONTESTANTS
    public function contestants($evid)
    {
        $ev_id = strip_tags($evid);
        $ev = self::check_security_event($ev_id);

        if($ev == false)
        {
            return view('error404');
        }

        $ct = Contestants::where('event_id',$ev_id)->get();
        return view('contestants',['data'=>$ct,'ev'=>$ev,'no'=>1]);
    }

    // EXPORT CONTESTANTS
    public function export_contestants($ev_id)
    {
        $ev = self::check_security_event($ev_id);

        if($ev == false)
        {
            return view('error404');
        }

        return (new ContestantExport($ev->id))->download('contestant.xlsx');
    }

    public function message_list($ev_id)
    {
        $messages = Messages::where([['ev_id',$ev_id],['user_id',Auth::id()]])->get();
        return view('message-list',['data'=>$messages]);
    }

    public function check_s3_image($img_url)
    {
        $check_url = get_headers(Storage::disk('s3')->url($img_url), 1);
        if($check_url[0] == 'HTTP/1.1 200 OK')
        {
            return Storage::disk('s3')->url($img_url);
        }
        else
        {
            return null;
        }
    }

    // DRAW CONTESTANTS
    public function draw_contestant(Request $request)
    {
        $id = strip_tags($request->id);
        $draw = strip_tags($request->draw);
        $ev_id = $request->ev_id;

        $ct = Contestants::where('contestants.id',$id)
            ->join('events','events.id','=','contestants.event_id')
            ->join('users','users.id','=','events.user_id')
            ->first();

        if(is_null($ct))
        {
            return response()->json(['err'=>1]);
        }

        try{
            $ctu = Contestants::find($id);
            $ctu->status = 1;
            $ctu->save();

            $event = Events::where([['id',$ev_id],['user_id',Auth::id()]])->first();
            $total_choosen_winner = $this->choosen_winner($ev_id);

            if($event->winners == $total_choosen_winner)
            {
                $evt = Events::find($ev_id);
                $evt->status = 3;
                $evt->save();
            }

            $res['err'] = 0;
        }
        catch(QueryException $e)
        {
            $res['err'] = 2;
        }

        return response()->json($res);
    }

    // DELETE CONTESTANTS
    public function del_contestant(Request $request)
    {
        $id = strip_tags($request->id);
        $draw = strip_tags($request->draw);
        $remove_winner = strip_tags($request->winner);

        $ct = Contestants::where('contestants.id',$id)
            ->join('events','events.id','=','contestants.event_id')
            ->join('users','users.id','=','events.user_id')
            ->first();

        if(is_null($ct))
        {
            return response()->json(['err'=>1]);
        }

        // if($draw !== null)
        // {
        //     return
        // }

        $cta = Contestants::find($id);

        // CASE IF USER WANT TO REMOVE WINNER
        if($remove_winner == 1)
        {
            try{
                $cta->status = 2;
                $cta->save();
                $res['err'] = 0;
            }
            catch(QueryException $e)
            {
                $res['err'] = 2;
            }
            return response()->json($res);
        }

        // DELETE CONTESTANTS
        try{
            Messages::where('ct_id',$id)->delete();
            $cta->delete();
            $res['err'] = 0;
        }
        catch(QueryException $e)
        {
            $res['err'] = 2;
        }

        return response()->json($res);
    }

    // DISPLAY WINNERS
    public function winner($ev_id)
    {
        $ev = self::check_security_event($ev_id);

        //check event
        if($ev == false)
        {
            return view('error404');
        }

        $ct = self::get_total_winner($ev);
        return view('contestants',['data'=>$ct,'ev'=>$ev,'no'=>1,'winner'=>true]);
    }

    // GET WINNERS ACCORDING ON EVENT TOTAL WINNERS
    public static function get_total_winner($ev)
    {
        $winners = $ev->winners;
        $ct = Contestants::where('event_id',$ev->id)->whereIn('status',[0,1])->orderBy('entries','desc')->orderBy('date_enter', 'asc')->skip(0)->take($winners)->get();
        return $ct;
    }

    // GET AWARDED WINNER
    public function choosen_winner($ev_id)
    {
        $ct = Contestants::where([['event_id',$ev_id],['status','=',1]])->orderBy('entries','desc')->orderBy('date_enter', 'asc')->get();
        return $ct->count();
    }

    // DUPLICATE EVENT
    public function duplicate_events(Request $request)
    {
        $tev = new CheckEvents;
        $evc = $tev::total_events(Auth::user()->membership);

        if(count($evc) > 0)
        {
            return response()->json($evc);
        }

        $ev_id = strip_tags($request->id);
        $ev = self::check_security_event($ev_id);

        //check event
        if($ev == false)
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

            $this->save_banner_image(null,$new_event_id,$duplicate);
        }
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

             // DELETE ENTIRE BANNER
            if(count($banners) > 0)
            {
                self::delete_banner(null,null,$banners);
            }

            // DELETE WA MESSAGE
            if($ev_check->img_url !== null)
            {
                Storage::disk('s3')->delete($ev_check->img_url);
            }

             // DELETE CORRELATED DATA
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

    public function redeem_money()
    {
        $helper = new Custom;
        $funds = $helper::redeem();
        return view('redeem',['funds'=>$funds,'helper'=>$helper]);
    }

    public function claim_money(Request $request)
    {
        $account = strip_tags($request->account);
        $number = strip_tags($request->number);
        $amount = strip_tags($request->amount);

        $helper = new Custom;
        $amount = $helper::redeem()[$amount];

        $auth = Auth::user();
        $redeem = new Redeem;
        $redeem->user_id = $auth->id;
        $redeem->name = $auth->name;
        $redeem->total = $amount;
        $redeem->account = $number;
        $redeem->account_name = $account;
        $redeem->withdrawal_method = 'DANA';

        try
        {
            $redeem->save();
            $res['success'] = 1;
        }
        catch(QueryException $e)
        {
            $res['success'] = 0;
        }

        return response()->json($res);
    }

    // CREATE GIVE AWAY
    public function create_giveaway()
    {
        $banners = $bonuses = array();
        $apicheck = false;
        $preloaded = null;
        $helper = new Custom;
        $user = Auth::user();

        $apicheck = self::check_api($user);
        $act = self::display_api('act');
        $mlc = self::display_api('mlc');

        $data = [
            'data'=>$banners,
            'preloaded'=>$preloaded,
            'bonus'=>$bonuses,
            'helper'=>$helper,
            'user'=>$user,
            'apicheck'=>$apicheck,
            'act'=>$act,
            'mlc'=>$mlc,
        ];
        return view('create',$data);
    }

    public static function check_api($user)
    {
        if($user->activrespon_api !== null || !empty($user->activrespon_api) || $user->mailchimp_api !== null || !empty($user->mailchimp_api))
        {
            return true;
        }
    }

    public static function display_api($cond)
    {
        $api = new API;

        // ACTIVRESPON
        if($cond == 'act')
        {
            if($api->get_activrespon_lists() == null)
            {
                return array();
            }

            return $api->get_activrespon_lists();
        }

        // MAILCHIMP
        if($cond == 'mlc')
        {
            if($api->display_mailchimp_lists() == null)
            {
                return array();
            }
            return $api->display_mailchimp_lists();
        }
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
        $event = Events::where([['events.id',$id],['users.id',Auth::id()]])->join('users','users.id','=','events.user_id')
                ->select('events.*','users.id AS user_id')->first();

        if(is_null($event))
        {
            return view('error404');
        }

        $helper = new Custom;
        $preloaded = null;
        $data = array();
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

        $user = Auth::user();
        $apicheck = self::check_api($user);
        $act = self::display_api('act');
        $mlc = self::display_api('mlc');

        // dd($act);

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
            'user'=>$user,
            'apicheck'=>$apicheck,
            'act'=>$act,
            'mlc'=>$mlc,
            'obj'=> new Homecontroller
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
        $message = strip_tags($request->message);

        $act_api_id = strip_tags($request->act_api_id);
        $mlc_api_id = strip_tags($request->mlc_api_id);
        ($act_api_id == null? $act_api_id = 0:false);
        ($mlc_api_id == null? $mlc_api_id = 0:false);

        ($request->media_option == 'off')?$mo = 1:$mo = 0;
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
            $ev = Events::where([['id',$request->edit],['user_id',Auth::id()]])->first();

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
        $ev->message = $message;
        $ev->act_api_id = $act_api_id;
        $ev->mlc_api_id = $mlc_api_id;

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

            $url_link = env('APP_URL')."/c/".$ev->url_link;
            Cookie::queue(Cookie::make('url',$url_link, 1*1));
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

        /* IMAGE WA */
        if($request->hasFile('media'))
        {
            self::save_wa_image($request,$event_id);
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

    // SAVE WA IMAGE MESSAGE
    public static function save_wa_image($request,$event_id)
    {
        $newfile = env('FOLDER_PATH').'/wa/'.Date('Y-m-d-h-i-s').".jpg";
        Storage::disk('s3')->put($newfile,file_get_contents($request->file('media')), 'public');

        //IN BROADCAST CASE
        if($event_id == null)
        {
            return $newfile;
        }

        //IN AUTO REPLY CASE
        $ev = Events::find($event_id);

        if($ev->img_url !== null)
        {
            Storage::disk('s3')->delete($ev->img_url);
        }

        $ev->img_url = $newfile;
        $ev->save();
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
    public function accounts(Request $request)
    {
        // PROFILE
        $helper = new Custom;
        $user = User::find(Auth::id());
        $conf = $request->segment(2);

        $data = ['user'=>$user,'helper'=>$helper,'lang'=>new Lang,'conf'=>$conf,'pc'=> new Custom,'cond'=>true,'account'=>1];
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

    // save user integration api
    public function save_api(Request $request)
    {
        $activrespon = strip_tags($request->act_api);
        $mailchimp = strip_tags($request->mail_api);

        $user = User::find(Auth::id());

        if($activrespon== null && $mailchimp == null)
        {
            return response()->json([]);
        }

        $user->activrespon_api = $activrespon;
        $user->mailchimp_api = $mailchimp;

        try
        {
            $user->save();
            $res['success'] = true;
        }
        catch(QueryException $e)
        {
            //$e->getMessage();
            $res['success'] = false;
        }

        return response()->json($res);
    }

    //CONTACT
    public function contact()
    {
        return view('contact');
    }

    public function save_contact(Request $request)
    {
        $user_email = Auth::user()->email;
        $message = strip_tags($request->message);

        $rule['message'] = ['required','max:255'];

        $validator = Validator::make($request->all(),$rule);
        if($validator->fails() == true)
        {
            $err = $validator->errors();

            $errs = [
                'err'=>1,
                'message'=>$err->first('message')
            ];

            return response()->json($errs);
        }

        Mail::to(Config::get('view.email_admin'))->send(new ContactEmail($user_email,$message));
        return response()->json(['err'=>0]);
    }

    //USER ORDER'S LIST
    public function order_list(Request $request)
    {
      $start = $request->start;
      $length = $request->length;
      $search = $request->search;
      $src = $search['value'];
      $data['data'] = array();

      if($src == null)
      {
         $orders = Orders::where('user_id',Auth::user()->id)->orderBy('created_at','desc')->skip($start)->limit($length)->get();
         $total = Orders::count();
      }
      else
      {
        if(preg_match("/^[a-zA-Z ]*$/i",$src) == 1)
        {
           $order = Orders::where('package','LIKE','%'.$src.'%');
        }
        elseif(preg_match("/[\-]/i",$src))
        {
          $order = Orders::where('created_at','LIKE','%'.$src.'%');
        }
        else
        {
          $order = Orders::where('no_order','LIKE',"%".$src."%");
        }

        $orders = $order->where('user_id',Auth::user()->id)->orderBy('created_at','desc')->skip($start)->limit($length)->get();
        $total = $orders->count();
    }

      // dd($orders);

      $data['draw'] = $request->draw;
      $data['recordsTotal']=$total;
      $data['recordsFiltered']=$total;
      $prc = new Custom;

      if($orders->count())
      {
        $no = 1;
        foreach($orders as $order)
        {
          if(($order->proof !== null && $order->status > 1) || ($order->proof == null && $order->status > 1))
          {
            $proof = '-';
          }
          elseif($order->proof == null || $order->status == 0)
          {
            $proof = '<button type="button" class="btn btn-info text-white btn-confirm" data-bs-toggle="modal" data-bs-target="#confirm-payment" data-id="'.$order->id.'" data-no-order="'.$order->no_order.'" data-package="'.$order->package.'" data-total="'.$order->total_price.'" data-date="'.$order->created_at.'" style="font-size: 13px; padding: 5px 8px;">'.Lang::get('order.confirm').'
              </button>';
          }
          else
          {
            $proof = '<a class="popup-newWindow" href="'.Storage::disk('s3')->url($order->proof).'">View</a>';
          }

          // STATUS ORDER
          if($order->status==1)
          {
            $status = '<span style="text-success"><b>'.Lang::get('order.process').'</b></span>';
          }
          elseif($order->status==2)
          {
            $status = '<span class="text-primary"><b>'.Lang::get('order.complete').'</b></span>';
          }
          elseif($order->status==3)
          {
            $status = '<span class="text-danger"><b>'.Lang::get('order.cancel').'</b></span>';
          }
          else
          {
            $status = '<span><b>'.Lang::get('order.waiting').'</b></span>';
          }

          if($order->date_confirm == null)
          {
            $date_confirm = '-';
          }
          else
          {
            $date_confirm = Carbon::parse($order->date_confirm)->toDateTimeString();
          }

          $data['data'][] = [
            0=>'<div class="text-center">'.$proof.'</div>',
            1=>$order->no_order,
            2=>$order->package,
            3=>$order->currency.".".$prc->format($order->price),
            4=>$order->currency.".".$prc->format($order->total_price),
            5=>Carbon::parse($order->created_at)->toDateTimeString(),
            6=>$date_confirm,
            7=>$order->desc,
            8=>$status
          ];
        }
      }

      echo json_encode($data);
    }

    //SAVE BRANDING ON USERS FIELD
    public function save_branding(Request $request)
    {
        $user = User::find(Auth::id());
        $dir = env('FOLDER_PATH').'/branding/'.explode(' ',trim($user->name))[0].'-'.$user->id;
        $filename = Carbon::now()->toDateTimeString()."-".$user->id.'.jpg';
        Storage::disk('s3')->put($dir."/".$filename, file_get_contents($request->file('logo_branding')), 'public');
        $user->branding = $dir."/".$filename;

        try{
            $user->save();
            $data['err'] = 0;
        }
        catch(QueryException $e)
        {
            $data['err'] = 1;
        }

        return response()->json($data);
    }

    // UPGRADE PACKAGE
    public function upgrade_package()
    {
        return view('package',['pc'=> new Custom,'cond'=>true,'account'=>0]);
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

    public static function check_security_event($ev_id)
    {
        $ev = Events::where([['id',$ev_id],['user_id',Auth::id()]])->first();

        //check event
        if(is_null($ev))
        {
            return false;
        }
        else
        {
            return $ev;
        }
    }

/* end of class */
}
