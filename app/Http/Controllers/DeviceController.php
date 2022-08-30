<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\QueryException;
use App\Helpers\Custom;
use App\Models\Phone;
use App\Models\User;
use App\Models\Messages;
use Carbon\Carbon;
use App\Helpers\Waweb;
use Symfony\Component\CssSelector\Parser\Token;
use chillerlan\QRCode\QRCode;

class DeviceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // DEVICE CONNECT PAGE
    public function connect_wa($id = null)
    {
        $phone = Phone::where('user_id',Auth::id())->get();
        $ct = new Custom;

        if($id == null)
        {
            return view('connect',['phone'=>$phone,'ct'=>$ct]);
        }
        else
        {
            $ph = Phone::find($id);
            if(is_null($ph))
            {
                return redirect('scan');
            }
            return view('connect-pair',['id'=>$id]);
        }
    }

    // CREATE DEVICE
    public function create_device()
    {
        $ph = Phone::where('user_id',Auth::id())->get();

        if($ph->count() >= 3)
        {
            return response()->json(['status'=>'max']);
        }

        $api = new Waweb;
        $ret = $api->create_device();

        if($ret == true)
        {
            $res['status'] = 1;
        }
        else
        {
            $res['status'] = 0;
        }

        return response()->json($res);
    }

    //CONNECT DEVICE
    public function connect(Request $request)
    {
        $api = new Waweb;
        $phone_id = strip_tags($request->phone_id);
        $phone = Phone::find($phone_id);
        $sc = $api->scan($phone_id);

        // if user does not pairing until time's up
        if(isset($sc['isConnected']))
        {
            if($sc['isConnected'] == 1)
            {
                self::update_phone($phone,$sc,null);
            }
            return response()->json($sc);
        }
    }

    // UPDATE PHONE STATUS
    public static function update_phone($phone,$sc,$from = null)
    {
        /* 
            $from = null
            from scan user,
            otherwise from check phone status
        */
        $user = User::find($phone->user_id);

        // IF ADMIN PHONE STATUS WOULD BE 3
        if($user->is_admin == 1)
        {
            $status = 3;
        }
        else
        {
            $status = $sc['isConnected'];
        }

        // in case if function call from connect()
        if($phone == null)
        {
            $phone->number = $sc['phone'];
        }
        $phone->status = $status;
        $phone->save();
    }

    // DISPLAYING QRCODE
    public function qrcode(Request $request)
    {
        $api = new Waweb;
        $phone_id = strip_tags($request->phone_id);
        $pair = $api->qr($phone_id);

        if($pair !== null)
        {
            $qr = new QRCode;
            return '<img style="width:200px" src="'.$qr->render($pair).'" alt="QR Code" />';
        }
        else
        {
            return 0;
        }
    }

    // CHECK AND CHANGE PHONE STATUS
    public function get_phone_status(array $data)
    {
        $api = new Waweb;
        $status = $api->status($data['phone_id']);

        if($status == 0)
        {
            return response()->json(['isConnected'=>'error']);
        }

        $phone = Phone::find($data['phone_id']);
        self::update_phone($phone,$status,1);
        return response()->json($status);
    }

    // SEND TEXT MESSAGE -- TEST MESSAGE PAGE
    public function send_message(Request $req)
    {
        $message = strip_tags($req->message);
        $img = strip_tags($req->media);
        $phone = strip_tags($req->code.$req->number);
        $sender = strip_tags($req->sender);

        $device = Phone::where('number',$sender)->first();
        if(is_null($device))
        {
            return response()->json(['error'=>1]);
        }

        $phone_id = $device->id;

        // LOGIC TO SEND MESSAGE
        $api = new Waweb;
        $send = $api->send_message($phone_id,$phone,$message,$img);
        return response()->json($send);
    }

    // DELETE DEVICE
    public function delete_device(Request $req)
    {
       $api = new Waweb;
       $phone_id = $req->phone_id;

       $del = $api->delete_device($phone_id);
       return response()->json(['status' => $del]);
    }

// end controller
}
