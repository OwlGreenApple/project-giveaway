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
use Carbon\Carbon;

class DeviceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->password = 'xa2D@!fg75C/p';
    }

    public function connect_wa()
    {
        $phone = Phone::where('user_id',Auth::id())->first();
        return view('connect',['phone'=>$phone]);
    }

    // SEND TEXT MESSAGE
    public function send_message(Request $req)
    {
        $data = [
            "to"=> $req->number,
            "message"=> $req->message,
            "reply_for"=> 0
        ];

        $phone = Phone::where('user_id',Auth::id())->first();
        $data_api = json_encode($data);
        $url = $phone->ip_server."/messages/send-text";

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

        $res = json_decode($result,true);
        return $res;
    }

    // SEND MEDIA IMAGE
    public function send_media(Request $req)
    {
        $data = [
            "to"=> $req->number,
            // "message"=> 'image message',
            // "media_url"=> 'https://cdn.pixabay.com/photo/2017/06/10/07/18/list-2389219_960_720.png',
            "message"=> $req->message,
            "media_url"=> $req->media,
            "type"=> 'image',
            "reply_for"=> 0
        ];

        $phone = Phone::where('user_id',Auth::id())->first();
        $data_api = json_encode($data);
        $url = $phone->ip_server."/messages/send-media";

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

        $res = json_decode($result,true);
        return $res;
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
            $user->email_wamate = $reg['email'];
            $user->wamate_id = $reg['id'];
            $wamate_email = $reg['email'];
        }
        else
        {
            $wamate_email = $user->email_wamate;
            $newreg = false;
        }
        
        // LOGIN DEVICE
        $login = $this->login_device($wamate_email);
        $user->token = $login['token'];
        $user->refresh_token = $login['refreshToken'];

        if($user->counter < 1)
        {
            if(Carbon::parse($user->date_counter)->lt(Carbon::now()->toDateString()) || $user->date_counter == null)
            {
                $user->counter = $ct->check_type($user->membership)['wa'];
                $user->date_counter = Carbon::now()->toDateString();
            }
        }

        try{
            $user->save();
            return self::create_device();
        }
        catch(QueryException $e)
        {
            // echo $e->getMessage();
            return 'err_token';
        }
    }

    // CREATE DEVICE
    public static function create_device()
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
        $url = Config::get('view.WAMATE_URL')."/devices";

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

        //INSERT NEW PHONE
        $device = new Phone;
        $device->user_id = Auth::id();
        $device->number = "0";
        $device->ip_server = Config::get('view.WAMATE_URL');
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
        $url = $phone->ip_server."/devices/".$device_id."/pair";

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
        return $result;
    }

    // CHECK AND CHANGE PHONE STATUS AND ALSO CAN DELETE DEVICE
    public function get_phone_status(Request $req)
    {
        $token = Auth::user()->token;
        $phone = Phone::where('user_id',Auth::id())->first();
        $device_id = $phone->device_id;
        $url = $phone->ip_server."/devices/".$device_id;

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

    //LOGIN DEVICE TO OBTAIN NEW TOKEN
    public function login_device($email)
    {
        // $email = 'local-2@loyalleads.com';
        $data = [
            'email'=>$email,
            'password'=>$this->password,
        ];

        $data_api = json_encode($data);
        $url = Config::get('view.WAMATE_URL')."/auth/login";

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
            'email'=>Config::get('view.WAMATE_EMAIL').'-'.Auth::id().'@loyalleads.com',
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
        return $response;
    }

// end controller
}
