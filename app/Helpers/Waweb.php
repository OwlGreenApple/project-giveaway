<?php
namespace App\Helpers;

use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use App\Models\Phone;
use App\Helpers\Server;
use Illuminate\Support\Facades\Config;

class Waweb
{
    public static function go_curl($url,$data,$method)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_VERBOSE, 0);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0);
        curl_setopt($ch, CURLOPT_TIMEOUT, 360);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);

        if($method == 'POST')
        {
            $data_string = json_encode($data);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        }

        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json')
        );

        $res=curl_exec($ch);
        return json_decode($res,true);
    }
    
    // CREATE DEVICE
    public function create_device() 
    {
        $label = self::generate_event_link();
        $user = Auth::user();
        $user_id = $user->id;
        //$ip = Server::port()[env('WA_SERVER')][0];
        $ip =  "localhost:3200";

        // CREATE DEVICE ON API WAWEB
        $api = self::get_key($ip,$user_id,$label);

        if((!isset($api['device_key']) && !isset($api['id'])) || (isset($api['device_key']) && $api['device_key'] == null))
        {
            return false;
        }

        // INSERT DEVICE TO PHONE
        $device = new Phone;
        $device->ip_server = $ip;
        $device->user_id = $user_id;
        $device->label = $label;
        $device->number = 0;
        $device->device_key = $api['device_key'];
        $device->device_id = $api['id'];

        try{
            $device->save();
            $ret = true;
        }
        catch(QueryException $e)
        {
            // dd($e->getMessage());
            $ret = false;
        }

        return $ret;
    }

    public static function get_key($server,$user_id,$label)
    {
        $url = $server."/create";
        $data = [
            'user_id'=>$user_id,
            'unique'=>$server.$user_id.$label
        ];

        $res = self::go_curl($url,$data,"POST");

        if(isset($res['device_key']) && isset($res['id']))
        {
            return $res;
        }

        $arr = array('device_key'=>null,'id'=>0);
        return $arr;
    }

    // SCAN DEVICE
    public function scan($phone_id)
    {
        $device = Phone::find($phone_id);

        if(is_null($device))
        {
            return 0;
        }

        $url = $device->ip_server.'/scan';
        $data = ["device_key"=>$device->device_key];
        $scan = self::go_curl($url,$data,'POST');
        return $scan;
    }

    // GET QRCODE AND THEN DISPLAYED
    public function qr($phone_id)
    {
        $device = Phone::find($phone_id);

        if(is_null($device))
        {
            return 0;
        }

        $url = $device->ip_server.'/qr?device_key='.$device->device_key.'';
        $qrcode = self::go_curl($url,null,'GET');

        return $qrcode;
    }

    // GET PHONE STATUS
    public function status($phone_id)
    {
        $device = Phone::find($phone_id);

        if(is_null($device))
        {
            return 0;
        }

        $url = $device->ip_server.'/status?id='.$device->device_key.'';
        $status = self::go_curl($url,null,'GET');
        return $status;
    }

    // PHONE LOGOUT
    public function logout($phone_id)
    {
        $device = Phone::find($phone_id);

        if(is_null($device))
        {
            return 0;
        }

        $url = $device->ip_server.'/logout?device_key='.$device->device_key.'';
        $status = self::go_curl($url,null,'GET');
        return $status;
    }

    // SEND MESSAGE OR MEDIA MESSAGE
    public function send_message($phone_id,$phone,$message,$img = null)
    {
        $device = Phone::find($phone_id);

        if(is_null($device))
        {
            return 0;
        }

        $url = $device->ip_server.'/message';
        $data = [
            'message'=>$message,
            //'unique'=>env('WA_UNIQUE'),
            //'unique'=>"Ww7YTPhDWVngJtaf87EdwCCguSKQ6hME",
            'unique'=>Config::get('view.WA_UNIQUE'),
            'device_key'=>$device->device_key,
            'number'=>str_replace("+","",$phone)
        ];

        // SEND MEDIA MESSAGE
        if($img == null)
        {
            //unset($data['url']);
        }
        else
        {
            $data['url'] = $img;
        }
        $send = self::go_curl($url,$data,'POST');
        return $send;
    }

    // DELETE DEVICE
    public function delete_device($phone_id)
    {
        $device = Phone::find($phone_id);
    
        if(is_null($device))
        {
            return 0;
        }

        //$url = $device->ip_server.'/del?device_key='.$device->device_key.'&unique='.env('WA_UNIQUE').'';
        $url = $device->ip_server.'/del?device_key='.$device->device_key.'&unique='.Config::get('view.WA_UNIQUE').'';
        $del = self::go_curl($url,null,'GET');

        if(isset($del['status']) && $del['status'] == 1)
        {
            try
            {
                Phone::find($device->id)->delete();
                $res = 1;
            }
            catch(QueryException $e)
            {
                //dd($e->getMessage());
                $res = 'error';
            }
        }
        else
        {
            $res = 0;
        }

        return $res;
    }

    // GENERATE LINK FOR LABEL ON DEVICE
    public static function generate_event_link()
    {
        $link = self::generate_random();
        $ev = Phone::where('label',$link)->first();
        if(is_null($ev))
        {
            return $link;
        }
        else
        {
            return self::generate_event_link();
        }
    }

    public static function generate_random()
    {
        $permitted_chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        return substr(str_shuffle($permitted_chars), 0, 12);
    }

/* end class */
}
