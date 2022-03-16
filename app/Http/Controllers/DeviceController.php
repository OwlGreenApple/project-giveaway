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

class DeviceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->password = 'xa2D@!fg75C/p';
    }

    private static function email_wamate()
    {
        return Config::get('view.WAMATE_EMAIL').'-'.Auth::id().'@'.env('APP_NAME').".app";
    }

    public function connect_wa()
    {
        $phone = Phone::where('user_id',Auth::id())->first();
        return view('connect',['phone'=>$phone,'ct'=>new Custom]);
    }

    // SEND TEXT MESSAGE
    public function send_message(Request $req)
    {
        $to = $req->number;
        $message = $req->message;

        // CASE IF USER USE TO TEST NUMBER
        if($req->code !== null)
        {
            if($to == null)
            {
                return response()->json(['err'=>1]);
            }

            $to = substr($req->code,1).$to;
        }

        if($message == null)
        {
            return response()->json(['err'=>1]);
        }

         // INVALID NUMBER FILTER
         if(is_numeric($to) == false)
         {
             return response()->json(['err'=>2]);
         }

        $user = User::find($req->user_id);

        // SPONSOR MESSAGE
        $ct = new Custom;
        if($user->membership == 'free' || $user->membership == 'starter' || $user->membership == 'starter-yearly')
        {
            $message.= $ct::sponsor();
        }

        $data = [
            "to"=> $to,
            "message"=> $message,
            "reply_for"=> 0
        ];

        $phone = Phone::where('user_id',$req->user_id)->first();
        $data_api = json_encode($data);
        $url = $user->ip_server."/messages/send-text";

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_api);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_VERBOSE, 0);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0);
        curl_setopt($ch, CURLOPT_TIMEOUT, 360);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'device-key: '.$phone->device_key
        ));

        $result = curl_exec($ch);
        if (curl_errno($ch) != 0 && empty($result))
        {
            return false;
        }

        curl_close($ch);
        $result = json_decode($result,true);

        //CUT QUOTA
        $user->counter_send_message_daily--;
        $user->save();

        if(!isset($result['id']))
        {
            return;
        }

        //IN CASE IF THIS CODE CALL FROM AUTO REPLY CRON NOT USER TEST SEND MESSAGE
        if($req->msg_id !== null)
        {
            $msg = Messages::find($req->msg_id);
            $msg->msg_id = $result['id'];
            $msg->status = 1;
            $msg->save();
        }
        else
        {
            return response()->json(['counter'=>$user->counter_send_message_daily]);
        }
    }

    // SEND MEDIA IMAGE
    public function send_media(Request $req)
    {
        // 'https://cdn.pixabay.com/photo/2017/06/10/07/18/list-2389219_960_720.png'

        $to = $req->number;
        $message = $req->message;
        $test = $req->test;

        // CASE IF USER USE TO TEST NUMBER
        if($req->code !== null)
        {
            if($to == null)
            {
                return response()->json(['err'=>1]);
            }

            $to = substr($req->code,1).$to;
        }

        if($message == null)
        {
            return response()->json(['err'=>1]);
        }

        // INVALID NUMBER FILTER
        if(is_numeric($to) == false)
        {
            return response()->json(['err'=>2]);
        }

        $user = User::find($req->user_id);

        // SPONSOR MESSAGE
        $ct = new Custom;
        if($user->membership == 'free' || $user->membership == 'starter' || $user->membership == 'starter-yearly')
        {
            $message.= $ct::sponsor();
        }

        // test = null which mean from broadcast or auto reply
        if($test == null)
        {
            $media = Storage::disk('s3')->url($req->media);
        }
        else
        {
            $media = $req->media;
        }

        $data = [
            "to"=> $to,
            "message"=> $message,
            "media_url"=> $media,
            "type"=> 'image',
            "reply_for"=> 0
        ];

        $phone = Phone::where('user_id',$req->user_id)->first();
        $data_api = json_encode($data);
        $url = $user->ip_server."/messages/send-media";

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_api);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_VERBOSE, 0);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0);
        curl_setopt($ch, CURLOPT_TIMEOUT, 360);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'device-key: '.$phone->device_key
        ));

        $result = curl_exec($ch);
        if (curl_errno($ch) != 0 && empty($result))
        {
            return false;
        }

        curl_close($ch);
        $result = json_decode($result,true);

        //CUT QUOTA
        $user->counter_send_message_daily--;
        $user->save();

        //IN CASE IF THIS CODE CALL FROM AUTO REPLY CRON NOT USER TEST SEND MESSAGE
        if($req->msg_id !== null)
        {
            $msg = Messages::find($req->msg_id);
            $msg->msg_id = $result['id'];
            $msg->status = 1;
            $msg->save();
        }
        else
        {
            return response()->json(['counter'=>$user->counter_send_message_daily]);
        }
    }

    //CONNECT DEVICE
    public function connect(Request $request)
    {
        $user = User::find(Auth::id());
        $ct = new Custom;

        // REGISTER DEVICE
        if($user->email_wamate == null)
        {
            $reg = $this->register_device();
            sleep(0.5);
        }
        else
        {
            $newreg = false;
        }

        // LOGIN DEVICE
        $login = $this->login_device();
        $user->token = $login['token'];
        $user->refresh_token = $login['refreshToken'];

        //TO PROTECT REFILL COUNTER DAILY MESSAGE IF USER LOGOUT THEN LOGIN
        if($user->counter_send_message_daily < 1)
        {
            if(Carbon::parse($user->date_counter)->lt(Carbon::now()->toDateString()) || $user->date_counter == null)
            {
                $user->counter_send_message_daily = $ct->check_type($user->membership)['wa'];
                $user->date_counter = Carbon::now()->toDateString();
            }
        }

        try{
            $user->save();
            return $this->create_device();
        }
        catch(QueryException $e)
        {
            // echo $e->getMessage();
            return 'err_token';
        }
    }

    // CREATE DEVICE
    public function create_device()
    {
        $ph = Phone::where('user_id',Auth::id())->first();

        if(!is_null($ph))
        {
            return response()->json(['status'=>'success']);
        }

        $data = [
            'name'=>'dv-'.Auth::id(),
        ];

        $token = Auth::user()->token;
        $data_api = json_encode($data);
        $url =  Auth::user()->ip_server."/devices";

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_api);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_VERBOSE, 0);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0);
        curl_setopt($ch, CURLOPT_TIMEOUT, 360);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Authorization: Bearer '.$token
        ));

        $result = curl_exec($ch);
        if (curl_errno($ch) != 0 && empty($result))
        {
            return false;
        }

        curl_close($ch);

        $res = json_decode($result,true);

        //EXPIRED TOKEN / INVALID TOKEN
        if(!isset($res['device_key']))
        {
            return response()->json(['status'=>'etoken']);
        }

        //INSERT NEW PHONE
        $device = new Phone;
        $device->user_id = Auth::id();
        $device->number = "0";
        $device->device_key = $res['device_key'];
        $device->device_id = $res['id'];

        try{
            $device->save();
        }
        catch(QueryException $e)
        {
            // echo $e->getMessage();
            return response()->json(['status'=>'cdevice']);
        }

        return response()->json(['status'=>'success']);
    }

    // PAIRING / SCAN DEVICE
    public function scan_device()
    {
        $phone = Phone::where('user_id',Auth::id())->first();
        $token = Auth::user()->token;
        $device_id = $phone->device_id;
        $url = Auth::user()->ip_server."/devices/".$device_id."/pair";

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_VERBOSE, 0);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0);
        curl_setopt($ch, CURLOPT_TIMEOUT, 360);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Authorization: Bearer '.$token
        ));

        $result = curl_exec($ch);
        if (curl_errno($ch) != 0 && empty($result))
        {
            return false;
        }

        curl_close($ch);
        // return qr-code html
        $result = json_decode($result,true);

        if($result['status'] == 'PAIRING')
        {
            return response()->json(['status'=>$result['status'],'qr_code'=>'<img src="'.$result['qr_code'].'" />']);
        }

        return response()->json(['status'=>$result['status'],'qr_code'=>0]);
    }

    // CHECK AND CHANGE PHONE STATUS AND ALSO CAN DELETE DEVICE
    public function get_phone_status(Request $req)
    {
        if($req->cron == null)
        {
            $token = Auth::user()->token;
            $user_id = Auth::id();
        }
        else
        {
            $token = User::find($req->user_id)->token;
            $user_id = $req->user_id;
        }

        $phone = Phone::where('user_id',$user_id)->first();
        $device_id = $phone->device_id;
        $user = User::find($user_id);
        $url = $user->ip_server."/devices/".$device_id;

        if($req->del == null)
        {
            $prequest = "GET";
        }
        else
        {
            $prequest = "DELETE";
        }

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $prequest);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_VERBOSE, 0);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0);
        curl_setopt($ch, CURLOPT_TIMEOUT, 360);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Authorization: Bearer '.$token
        ));

        $result = curl_exec($ch);
        if (curl_errno($ch) != 0 && empty($result))
        {
            return false;
        }

        curl_close($ch);
        $response = json_decode($result,true);

        $ph = Phone::find($phone->id);

        // IN CASE OF DELETE
        if($response == null)
        {
            $ph->delete();
            return response()->json(['status'=>'success']);
        }

        // IN CASE OF CHECK STATUS
        if($response['status'] == 'PAIRED')
        {
            $ph->number = $response['phone'];
            $ph->status = 1;
            $res['status']='success';
        }
        else
        {
            $ph->status = 0;
            $res['status']=0;
        }

        $ph->save();
        return response()->json($res);
    }

    // REFRESH TOKEN
    public function refresh($api = null)
    {
        $auth = Auth::user();

        if($auth->refresh_token == null)
        {
            return response()->json(['err'=>'rtoken']);
        }

        $data = [
            'refresh_token'=>$auth->refresh_token
        ];

        $data_api = json_encode($data);
        $url = $auth->ip_server."/auth/refresh";

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_api);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_VERBOSE, 0);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0);
        curl_setopt($ch, CURLOPT_TIMEOUT, 360);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json'
        ));

        $result = curl_exec($ch);
        if (curl_errno($ch) != 0 && empty($result))
        {
            return false;
        }

        curl_close($ch);

        $response = json_decode($result,true);

        // IF INVALID TOKEN OR EXPIRED -- solution : login wamate
        if(isset($response['status']) == 401)
        {
            return response()->json(['err'=>'itoken']);
        }

        if(isset($response['token']))
        {
            $user = User::find($auth->id);
            $user->token = $response['token'];
            $user->refresh_token = $response['refreshToken'];
            $user->save();
        }

        if($api == null)
        {
            return response()->json(['err'=>0]);
        }
    }

    //LOGIN DEVICE TO OBTAIN NEW TOKEN AND REFRESH TOKEN
    public function login_device()
    {
        // $email = 'local-2@loyalleads.com';
        $data = [
            'email'=>self::email_wamate(),
            'password'=>$this->password,
        ];

        $data_api = json_encode($data);
        $url =  Auth::user()->ip_server."/auth/login";

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_api);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_VERBOSE, 0);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0);
        curl_setopt($ch, CURLOPT_TIMEOUT, 360);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json'
        ));

        $result = curl_exec($ch);
        if (curl_errno($ch) != 0 && empty($result))
        {
            return false;
        }

        curl_close($ch);

        $response = json_decode($result,true);
        return $response;
    }

    //REGISTER USER WAMATE
    public function register_device()
    {
        $data = [
            'email'=>self::email_wamate(),
            'password'=>'xa2D@!fg75C/p',
        ];

        $data_api = json_encode($data);
        $url = Config::get('view.WAMATE_URL')."/auth/register";

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_api);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_VERBOSE, 0);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0);
        curl_setopt($ch, CURLOPT_TIMEOUT, 360);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json'
        ));

        $result = curl_exec($ch);
        if (curl_errno($ch) != 0 && empty($result))
        {
            return false;
        }

        curl_close($ch);

        $response = json_decode($result,true);
        $user = User::find(Auth::id());

        try{
            $user->email_wamate = $response['email'];
            $user->wamate_id = $response['id'];
            $user->ip_server = Config::get('view.WAMATE_URL');
            $user->save();
        }
        catch(QueryException $e)
        {
            // echo $e->getMessage();
            return 'err_reg_db';
        }

        return $response;
    }

// end controller
}
