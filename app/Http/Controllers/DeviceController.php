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

class DeviceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->password = 'xa2D@!fg75C/p';
    }

    public function connect_wa()
    {
        return view('connect');
    }

    public function connect(Request $request)
    {
        $phone = $request->code.$request->phone;
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
        }
        
        // LOGIN DEVICE
        $login = $this->login_device($wamate_email);
        $user->token = $login['token'];
        $user->refresh_token = $login['refreshToken'];
        $user->counter = $ct->check_type($user->membership)['wa'];

        try{
            $user->save();
        }
        catch(QueryException $e)
        {
            echo $e->getMessage();
            return response()->json(['err'=>'token']);
        }
        
        // $device = new Phone;
        // $device->user_id = Auth::id();
        // $device->number = Auth::id();
        // $device->ip_server = Config::get('view.WAMATE_URL');
        // $device->device_key = Auth::id();
        // $device->device_id = Auth::id();
        // $device->save();
    }

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
