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

class AffiliateController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function create_affiliate()
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

    public function save_affiliate()
    {
        $user = Auth::user();
        $check_user = true;
        while (!is_null($check_user)) {
          $referral_code = substr(md5(microtime()),rand(0,26),8);
          $referral_code = str_replace("0","o",$referral_code);
          $referral_code = strtoupper($referral_code);
          $check_user = User::where("referral_code",$referral_code)->first();
        }
        $user->referral_code = $referral_code;
        $user->save();

        return response()->json([
            "success"=>1,
            "message"=>"success",
            "referral_code"=>$referral_code,
        ]);
    }
/* end class */
}
