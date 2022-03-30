<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use App\Helpers\Custom;
use App\Models\User;
use App\Models\Orders;
use App\Models\Redeem;
use App\Models\Membership;
use App\Mail\UserBuyEmail;
use Carbon\Carbon;

class AdminRedeemController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('is_admin');
    }

    public function index()
    {
      return view('admin.redeem.index');
    }

    public function affiliate_admin_data()
    {
      $redeem = Redeem::orderBy('id','desc')->get();
      return view('admin.redeem.content',['data'=>$redeem]);
    }


    public function upload_payment_2(Request $request)
    {
      $redeem_id = $request->id;

      $redeem = Redeem::find($redeem_id);
      $redeem->is_paid = 1;
      $redeem->withdrawal_method = $request->withdrawal_method;

      $user = User::find($redeem->user_id);
      $user->money -= $redeem->total;

      try{
        $user->save();
        $redeem->save();
        $data['err'] = 0;
      }
      catch(QueryException $e)
      {
        // $e->getMessages();
        $data['err'] = 1;
      }

      return response()->json($data);
    }

/* end controller */
}
