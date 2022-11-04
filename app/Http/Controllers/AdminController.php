<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use App\Helpers\Custom;
use App\Models\User;
use App\Models\Orders;
use App\Models\Membership;
use App\Models\Settings;
use App\Models\Phone;
use App\Mail\UserBuyEmail;
use App\Models\Contestants;
use App\Models\Events;
use Carbon\Carbon;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('is_admin');
    }

    public function index()
    {
        return view('admin.index');
    }

    //load and display user from ajax
    public function display_users()
    {
        $data = array();
        $users = User::where('is_admin',0)->get();
         
        if($users->count() > 0)
        {
          foreach($users as $row):
            $events = Events::where('user_id',$row->id);
            $ev_id = $events->select('id')->get()->toArray();
            $total_contestants = Contestants::whereIn('event_id',$ev_id)->get()->count();

            $data[] = [
              'id'=>$row->id,
              'name'=>$row->name,
              'email'=>$row->email,
              'membership'=>$row->membership,
              'total_giveaway'=>$events->count(),
              'total_ct'=>$total_contestants,
              'end_membership'=>$row->end_membership,
              'created_at'=>$row->created_at,
              'status'=>$row->status,
            ];
          endforeach;
        }

        return view('admin.users',['users'=>$data,'no'=>1]);
    }

    public function ban_user(Request $request)
    {
      $user = User::find($request->id);
      if(is_null($user))
      {
        $data['error'] = 2;
        $data['msg'] = 'User tidak ada';
        return response()->json($data);
      }

      try
      {
        $user->status = 0;
        $user->save();
        $data['error'] = 0;
      }
      catch(QueryException $e)
      {
        $data['error'] = 1;
        $data['msg'] = $e->getMessage();
      }
      return response()->json($data);
    }

    public function settings()
    {
      $settings = Settings::all()->first();
      $helper = new Custom;
      return view('admin.settings',['row'=>$settings,'ct'=>$helper]);
    }

    public function settings_save(Request $request)
    {
      try
      {
        Settings::where('id',1)->update([
          'percentage'=>strip_tags($request->percentage),
          'sponsor'=>strip_tags($request->sponsor_message),
          'changed_by'=>Auth::id(),
        ]);
        $ret['success'] = 1;
      }
      catch(QueryException $e)
      {
         $e->getMessage();
         $ret['success'] = 0;
      }

      return response()->json($ret);
    }

    // SAVE ADMIN PHONE
    public function settings_phone(Request $request)
    {
      $ct = new Custom;
      if($request->phone_id == null)
      {
        $phone = new Phone;
        $phn_update = 0;
      }
      else
      {
        $phone = Phone::find($request->phone_id);
        $phn_update = 1;
      }

      // phone status
      if($request->user == null)
      {
        $status = 3; // IF PHONE CREATED BY ADMIN
      }
      else
      {
        $status = 1; // IF PHONE CREATED BY USER
      }

      if(Auth::user()->is_admin == 1 && $request->user == null)
      {
        $phone_number = strip_tags($request->phone);
        $phn = $phone_number;
      }
      else
      {
        $phn = strip_tags($request->phone);
        $code = strip_tags($request->pcode);
        $phone_number = $code.$phn;
        $phone_number = substr($phone_number,1);
      }

      // in case phone == null (when user doesn't want to update phone)
      if(strlen($phn) > 0)
      {
        $phone->number = $phone_number;
      }

      $wablas_server = strip_tags($request->wablas);

      //  in case if data come from user
      if($wablas_server == null)
      {
        $wablas_server = 0;
      }

      $phone->user_id = Auth::id();
      $phone->device_key = strip_tags($request->api_key);
      $phone->service_id = strip_tags($request->service);
      $phone->device_id = $wablas_server;
      $phone->status = $status;

      try
      {
         $phone->save();
         $ret['success'] = 1;
         $ret['phone_update'] = $phn_update;
         $ret['phone'] = $phone_number;
      }
      catch(QueryException $e)
      {
        //  dd($e->getMessage());
         $ret['success'] = 0;
      }

      return response()->json($ret);
    }

    public function display_admin_phone()
    {
      $phone = Phone::where('status',3)->get();
      $helper = new Custom;
      return view('admin.phone',['data'=>$phone,'no'=>1,'ct'=>$helper]);
    }

    // DELETE ADMIN PHONE
    public function del_admin_phone(Request $req)
    {
      $phone = Phone::find($req->id);
      try
      {
        $phone->delete();
        $ret['success'] = 1;
      }
      catch(QueryException $e)
      {
        $e->getMessage();
        $ret['success'] = 0;
      }
      return response()->json($ret);
    }

    /*** ORDER ***/
    public function order_list()
    {
        return view('admin.order.index');
    }

    public function order(Request $request)
    {
      $start = $request->start;
      $length = $request->length;
      $search = $request->search;
      $src = $search['value'];
      $data['data'] = array();

      if($src == null)
      {
         $orders = Orders::orderBy('created_at','desc')->skip($start)->limit($length)->get();
         $total = Orders::count(); //use this instead of ->count(), this cause error when in case large amount data.
      }
      else
      {
        if(preg_match("/^ACT[a-zA-Z0-9]/i",$src))
        {
          $order = Orders::where('no_order','LIKE',"%".$src."%");
        }
        elseif(preg_match("/[a-zA-Z]/i",$src))
        {
          $order = Orders::where('package','LIKE',"%".$src."%");
        }
        elseif(preg_match("/[\-]/i",$src))
        {
          $order = Orders::where('created_at','LIKE','%'.$src.'%');
        }
        else
        {
          $order = Orders::where('notes','LIKE',"%".$src."%");
        }

        $orders = $order->orderBy('created_at','desc')->skip($start)->limit($length)->get();
        $total = $orders->count();
      }

      $data['draw'] = $request->draw;
      $data['recordsTotal']=$total;
      $data['recordsFiltered']=$total;
      $api = new Custom;

      if($orders->count())
      {
        foreach($orders as $row)
        {
          if($row->notes !== null)
          {
          	$notes = '<textarea>'.$row->notes.'</textarea>';
          }
          else
          {
          	$notes = '-';
          }

          if($row->status == 0)
          {
            $confirm = '<b class="text-default">Menunggu Konfirmasi</b>';
          }
          elseif($row->status == 1)
          {
          	$confirm = '<button type="button" class="btn btn-info btn-sm confirm" data-bs-toggle="modal" data-bs-target="#confirm_popup" data-id="'.$row->id.'">Konfirmasi</button> <button type="button" class="btn btn-danger btn-sm cancel" data-bs-toggle="modal" data-bs-target="#cancel_popup" data-id="'.$row->id.'">Batal</button>';
          }
          elseif($row->status == 2)
          {
            $confirm = '<b class="text-success">Terkonfirmasi</b>';
          }
          elseif($row->status == 3)
          {
          	$confirm = '<b class="text-danger">Batal</b>';
          }
          else
          {
            $confirm = '<b class="text-danger">Batal oleh system</b>';
          }

          // PROOF
          if($row->proof !== null)
          {
          	$proof = '<a class="popup-newWindow" href="'.Storage::disk('s3')->url($row->proof).'">Lihat</a>';
          }
          else
          {
          	$proof = '-';
          }

          // DATE CONFIRM
          if($row->date_confirm == null)
          {
            $date_confirm = '-';
          }
          else
          {
            $date_confirm = $row->date_confirm;
          }

          $data['data'][] = [
            0=>$row->no_order,
            1=>$row->package,
            2=>$api->format($row->price),
            3=>$api->format($row->total_price),
            4=>$proof,
            5=>$notes,
            6=>Carbon::parse($row->created_at)->toDateTimeString(),
            7=>$date_confirm,
            8=>$confirm
          ];
        }
      }

      echo json_encode($data);
    }

    /*
      ADMIN CONFIRM ORDER
    */
    public function confirm_order(Request $request)
    {
    	$order = Orders::find($request->id);
      $ct = new Custom;

    	if(!is_null($order))
    	{
        $today = Carbon::now();
        $user = User::find($order->user_id);

        $order->date_confirm = $today;
        $order->status = 2;
        $order->save();

        // $check_active_membership = $this->check_term_membership($user);
        $total_month = $ct->check_type($order->package)['terms'];

        //referrer get money from referral
        if($user->myreferral > 0)
        {
          $percentage = Settings::find(1)->percentage;
          $money = $percentage * $order->total_price / 100;

          $refferer = User::find($user->myreferral);
          $refferer->money += round($money);
          $refferer->save();
        }

        /*
        --- order later ---
        if($check_active_membership == 'active')
        {
            $data = [
                'user_id'=>$user->id,
                'order_id'=>$order->id,
                'order_package'=>$order->package,
                'terms'=>$total_month
            ];
            // send mail to user order later
            $ct->mail($user->email,new UserBuyEmail($order,$user->name),null);
            return $this->orderLater($data);
        }
        else
        { */
            $user->membership = $order->package;
            $user->end_membership = $today->addMonths($total_month);
            $user->status = 2;
            $user->save();
        // }

        // send mail to user
        $ct->mail($user->email,new UserBuyEmail($order,$user->name),null);
        $data['success'] = 1;
    	}
    	else
    	{
    		$data['msg'] = Lang::get('order.id');
    		$data['success'] = 0;
    	}

    	return response()->json($data);
    }

    /*
      TO CHECK WHETHER MEMBERSHIP STILL ACTIVE OR HAS END,
      IF ACTIVE, ORDER / PURCHASED ORDER WILL DELIVER TO TABLE MEMBERSHIP
    */
    private function check_term_membership($user)
    {
      if(!is_null($user))
      {
        $user_status = $user->status;
        if($user_status == 2)
        {
          return 'active';
        }
        else
        {
          return 'inactive';
        }
      }
    }

    /*
       DELIVER ORDER TO TABLE MEMBERSHIP
    */
    private function orderLater(array $data)
    {
      $check_membership = Membership::where('user_id',$data['user_id'])->orderBy('id','desc')->first();

      $membership = new Membership;
      $membership->user_id = $data['user_id'];
      $membership->order_id = $data['order_id'];

      if(is_null($check_membership))
      {
        $getDay = $this->updateLater($data['user_id']);
        $membership->start = $getDay['start'];
        $membership->end = $getDay['end'];
        $membership->status = 0;
      }
      else
      {
        //if available data
        $previous_end_day = Carbon::parse($check_membership->end)->setTime(0, 0, 0);
        $next_end_day = Carbon::parse($previous_end_day)->addMonths($data['terms']);

        $membership->start = $previous_end_day;
        $membership->end = $next_end_day;
      }

      try
      {
        $membership->save();
        $arr['success'] = 1;
      }
      catch(QueryException $e)
      {
        $arr['msg'] = Lang::get('order.err_membership');
        $arr['success'] = 0;
      }

      return response()->json($arr);
    }

    /*
      ORDER LATER
    */
    public function updateLater($user_id)
    {
        $user = User::find($user_id);
        $end_membership = $user->end_membership;
        $data['start'] = Carbon::parse($end_membership)->toDatetimeString();
        $data['end'] = Carbon::parse($data['start'])->setTime(0,0,0)->addMonths(1);
        return $data;
    }

    //CANCEL ORDER
    public function cancel_order(Request $request)
    {
    	$order = Orders::find($request->id);
    	if(!is_null($order))
    	{
    		$order->status = 3;
    		$order->save();
    		$data['success'] = 1;
    	}
    	else
    	{
    		$data['msg'] = Lang::get('order.id');
    		$data['success'] = 0;
    	}

    	return response()->json($data);
    }

    // METRIC NGINX
    public function index_metrics_chart() {
      return view('admin.metrics-index');
    }

    public function generate_metrics_chart(Request $request){
      exec("goaccess /var/log/nginx/access.log -o /var/www/watcherviews.com/public_html/783213900.html --log-format=COMBINED > /dev/null 2>&1 & ");

      return json_encode([
        "success"=>1,
        "message"=>"data saved!",
      ]);
    }

/* end controller */
}
