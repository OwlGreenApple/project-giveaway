<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\QueryException;
use App\Helpers\Custom;
use App\Helpers\Api;
use App\Models\Orders;
use App\Models\Notification;
use App\Mail\MembershipEmail;
use Carbon\Carbon;

class OrderController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth', ['only' => ['confirm_payment_order']]);
    }

    public function index()
    {
    	// REMOVE SESSION IF AVAILABLE
    	if(session('order') !== null)
    	{
    		Session::forget('order');
    	}

    	$page = request()->segment(2);
    	$page == null? $page = 1 : $page;
    	$part = [
    		'api'=> new Custom,
    		'page'=>$page
    	];
    	return view('order.checkout',$part);
    }

    // SUBMIT PAYMENT
    public function submit_payment(Request $request)
    {
        $package = $request->package;
        $is_ajax = false;
        $price = new Custom;
        $pack = $price->check_type($package);

        // in case if checkout from summary
        if(session('order') !== null)
        {
            $order = [
                "package" => session('order')['package'],
                "price" => session('order')['price'],
                "total" => session('order')['price'],
            ];
        }
        else
        {
            $order = [
                "package" => $package,
                "price" => $pack['price'],
                "total" => $pack['price'],
            ];
        }

        if(session('order') == null)
        {
          session(['order'=>$order]);
        }

        if($request->ajax() == true)
        {
            $is_ajax = true;
        }

        if(Auth::check() == false)
        {
          $rt['status'] = 1; // redirect to summary
          return response()->json($rt);
        }
        else
        {
          $order['email'] = Auth::user()->email;
          return $this->submit_order($order,$is_ajax);
        }
    }

    // SUMMARY PAGE IF USER NOT LOGGED IN
    public function summary()
    {
        if(session('order') == null)
        {
           return redirect('checkout');
        }

        $session = session('order');
        return view('order.summary',['session'=>$session,'lang'=>new Lang,'api'=>new Custom]);
    }

    // WHEN USER HAS LOGED IN ALREADY
    public function submit_order(array $data,$is_ajax = false)
    {
        $dt = Carbon::now();
        $str = 'ACT'.$dt->format('ymd').'-'.$dt->format('Hi');
        
        $order = new Orders;
        $order_number = self::autoGenerateID($order, 'no_order', $str, 3, '0');
        $order->user_id = Auth::id();
        $order->no_order = $order_number;
        $order->package = $data['package'];
        $order->price = $data['price'];
        $order->total_price = $data['total'];

        try
        {
            $order->save();
            if(Auth::id() == null)
            {
                $rt['status'] = 1; //redirect to summary page
            }
            else
            {
                $rt['status'] = 2; //redirect to thankyou page

                // SEND WA MESSAGE IF ORDER SUCCESSFUL  
                // $this->send_message($data['package'],$data['price'],$data['total'],$order_number,Auth::user()->phone_number);

                // SEND EMAIL IF ORDER SUCCESSFUL
                Mail::to($data['email'])->send(new MembershipEmail($order_number,Auth::user()->name,$data['package'],$data['price'],$data['total']));
            }

            if(session('order') !== null)
            {
                Session::forget('order');
            }
        }
        catch(QueryException $e)
        {
             // dd($e->getMessage());
            $rt['status'] = 0;
            $rt['msg'] = Lang::get('custom.failed');
        }

        // in case of ajax
        if($is_ajax == true)
        {
            return response()->json($rt);
        }
        else
        {
            if($rt['status'] = 2)
            {
                return redirect('thankyou');
            }
            else
            {
                return redirect('summary')->with('status', Lang::get('custom.failed'));
            }
        }
    }

    // GENERATE ID FOR ORDER NUMBER
    public static function autoGenerateID($model, $field, $search, $pad_length, $pad_string = '0')
    {
        $tb = $model->select(DB::raw("substr(".$field.", ".strval(strlen($search)+1).") as lastnum"))->whereRaw("substr(".$field.", 1, ".strlen($search).") = '".$search."'")->orderBy('id', 'DESC')->first();
                                        
        if ($tb == null){
            $ctr = 1;
        }
        else{
            $ctr = intval($tb->lastnum) + 1;
        }
        return $search.'-'.str_pad($ctr, $pad_length, $pad_string, STR_PAD_LEFT);
    }

    // SEND MESSAGE TO USER'S WA
    public function send_message($package,$price,$total,$order_number,$phone_number,$after = null)
    {
        $pc = new Custom;
        $api = new Api;
        $notif = Notification::all()->first();
        $admin_id = $notif->admin_id;

        // after = if NOT null message is for 6 hours after order
        if($after == null)
        {
            $txt = $notif->notif_order;
        }
        else
        {
            $txt = $notif->notif_after;
        }
       
        $message = $this->replace_string_order($package,$pc->pricing_format($price),$pc->pricing_format($total),$order_number,$txt);

        $api->send_wa_message($admin_id,$message,$phone_number);
    }

    // REPLACE SPECIAL CHARACTER ACCORDING ON ORDER
    public function replace_string_order($package,$price,$total,$order_number,$message)
    {
        $replace_target = array(
          '[PACKAGE]','[PRICE]','[TOTAL]','[NO-ORDER]'
        );

        $replace = array(
          $package,$price,$total,$order_number
        );

        return str_replace($replace_target,$replace,$message);
    }

    public function thankyou()
    {
        self::no_auth();
        return view('order.thankyou');
    }

    private static function no_auth()
    {
        if(Auth::check() == false)
        {
            return redirect('checkout');
        }
    }

   // ORDER PAGE

    //upload bukti TT 
      public function confirm_payment_order(Request $request){
        $user = Auth::user();
        //konfirmasi pembayaran user
        $order = Orders::find($request->id_confirm);
        $pathUrl = str_replace(url('/'), '', url()->previous());

        if(strlen($request->keterangan) > 300)
        {
            return redirect($pathUrl)->with("error", $this->lang::get('order.max_notes'));
        }

        if($order->status==0)
        {
          $order->status = 1;

          if($request->hasFile('buktibayar'))
          {
            // $path = Storage::putFile('bukti',$request->file('buktibayar'));
            $dir = env('FOLDER_PATH').'/bukti_bayar/'.explode(' ',trim($user->name))[0].'-'.$user->id;
            $filename = $order->no_order.'.jpg';
            Storage::disk('s3')->put($dir."/".$filename, file_get_contents($request->file('buktibayar')), 'public');
            $order->proof = $dir."/".$filename;
            
          } else {
            return redirect($pathUrl)->with("error", Lang::get('order.upload_first'));
          }  
          $order->notes = $request->keterangan;
          $order->save();
        } 
        else 
        {
            return redirect($pathUrl)->with("error", Lang::get('order.reject'));
        }

        return view('order.thankyou-confirm-payment',['lang'=>new Lang]);
      }

/*end class*/
}
