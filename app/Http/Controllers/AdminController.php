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
use App\Models\Membership;
use App\Mail\UserBuyEmail;
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
        $users = User::all()->where('is_admin',0);
        return view('admin.index',['users'=>$users,'no'=>1]);
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

        $orders = $order->orderBy('created_at','desc')->get();
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
          	$confirm = '<button type="button" class="btn btn-info btn-sm confirm" data-bs-toggle="modal" data-bs-target="#confirm_popup" data-id="'.$row->id.'">Konfirmasi</button> <button type="button" class="btn btn-danger btn-sm cancel" data-toggle="modal" data-target="#cancel_popup" data-id="'.$row->id.'">Batal</button>';
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

    	if(!is_null($order))
    	{
            $today = Carbon::now();
            $user = User::find($order->user_id);
        
            $order->date_confirm = $today;
            $order->status = 2;
            $order->save();

            $user = User::find($order->user_id);
            $check_active_membership = $this->check_term_membership($user);

            // dd($user->id);
            
            if($check_active_membership == 'active')
            {
            $data = [
                'user_id'=>$user->id,
                'order_id'=>$order->id,
                'order_package'=>$order->package
            ];
                // send mail to user order later
                Mail::to($user->email)->send(new UserBuyEmail($order,$user->name));
                return $this->orderLater($data);
            }
            else
            {
            $user->membership = $order->package;
            $user->end_membership = $today->addMonths(1);
            $user->status = 2;
            $user->save();
            }

            // send mail to user
            Mail::to($user->email)->send(new UserBuyEmail($order,$user->name));
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
      TO CHECK WHETTHER MEMBERSHIP STILL ACTIVE OR HAS END,
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
        $next_end_day = Carbon::parse($previous_end_day)->addMonths(1);
        
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

/* end controller */
}