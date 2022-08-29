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
    public function connect_wa()
    {
        $phone = Phone::where('user_id',Auth::id())->first();
        return view('connect',['phone'=>$phone,'ct'=>new Custom]);
    }

    // CREATE DEVICE
    public function create_device()
    {
        $ph = Phone::where('user_id',Auth::id())->first();

        if(!is_null($ph))
        {
            return response()->json(['status'=>'error']);
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
    public function connect()
    {
        $api = new Waweb;
        $sc = $api->scan();
        $phone = Phone::where('user_id',Auth::id())->first();

        // if user does not pairing until time's up
        if(isset($sc['isConnected']))
        {
            if($sc['isConnected'] == 1)
            {
                $device = Phone::find($phone->id);
                $device->number = $sc['phone'];
                $device->status = $sc['isConnected'];
                $device->save();
            }
            return response()->json($sc);
        }
    }

    // DISPLAYING QRCODE
    public function qrcode()
    {
        $api = new Waweb;
        $pair = $api->qr();

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
    public function get_phone_status($user_id)
    {
        $api = new Waweb;
        $status = $api->status();
        $phone = Phone::where('user_id',$user_id)->first();

        if(is_null($phone))
        {
            return response()->json(['isConnected'=>'error']);
        }

        $device = Phone::find($phone->id);
        $device->number = $status['phone'];
        $device->status = $status['isConnected'];
        $device->save();

        return response()->json($status);
    }

    // SEND TEXT MESSAGE -- TEST MESSAGE PAGE
    public function send_message(Request $req)
    {
        $user_id = Auth::id();
        $message = strip_tags($req->message);
        $img = strip_tags($req->media);
        $phone = strip_tags($req->code.$req->number);

        // LOGIC TO SEND MESSAGE
        $api = new Waweb;
        $send = $api->send_message($user_id,$phone,$message,$img);
        return response()->json($send);
    }

    // DELETE DEVICE
    public function delete_device()
    {
       $api = new Waweb;
       $user_id = Auth::id();

       $del = $api->delete_device($user_id);
       return response()->json(['status' => $del]);
    }

// end controller
}
