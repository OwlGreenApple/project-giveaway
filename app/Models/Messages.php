<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Phone;
use app\Models\Settings;
use App\Helpers\Custom;
use App\Helpers\Waweb;

class Messages extends Model
{
    use HasFactory;

    protected $table = 'messages';

    /*
        status :
        0 == message not send
        1 == message sent
        2 == message delivered
        3 == message READ
        4 == device not available or disconnected
        5 == case winner if user winning_run = 1 but user's phone inactive

        device_id : is not api device id but id from table phone
    */

    //  DETERMINE ADMIN NUMBER TO SEND MESSAGE
    public static function sender($user_id)
    {
        $ph = Phone::where('user_id',$user_id)->first();
        $user = User::find($user_id);
        $package = $user->membership;

        // in case if user has deleted his token or package is free, then using admin phone number
        if(is_null($ph) || $package == 'free')
        {
            $phn = Phone::where('users.is_admin',1)
                ->join('users','users.id','=','phones.user_id')
                ->select('phones.number')
                ->inRandomOrder()->first();
            $sender = $phn->number;
        }
        else
        {
            $sender = $ph->number;
        }

        return $sender;
    }

    public static function sendingwa($user,$customer_phone,$customer_message,$image,$sender)
    {
      // to avoid error user_id = 0
      $cs = new Custom;

      if(!is_null($user))
      {
        $package = $user->membership;
        $category = $cs->check_type($package);

        if($category['sponsor'] == 1 || $user->is_admin == 1)
        {
            $customer_message .= $cs::sponsor(null);
        }
      }

      $ph = Phone::where([['number',$sender],['status','>',0]])->first();

      // in case if user has deleted his token or package is free, then using admin phone number
      if(is_null($ph) || $package == 'free')
      {
        $phn = Phone::where('status',3)->inRandomOrder()->first();

        // RETURN 4 IF PHONE DISCONNECT OR NOT AVAILABLE in case service_id = 0
        if(is_null($phn))
        {
          $ret = array(
            'status'=>4,
            'device_id'=>0
          );
          return $ret;
        }

        $token =  $phn->device_key;
        $service = $phn->service_id;
        $wablas_server = $phn->device_id;
        $phone_id = $phn->id;
      }
      else
      {
        // user's phone api number
        $token =  $ph->device_key;
        $service = $ph->service_id;
        $wablas_server = $ph->device_id;
        $phone_id = $ph->id;
      }

    //   package free = in case if user membership has reach end then turn to free, then use admin number
      // dd($ph);

      $data = [
        'token'=>$token,
        'to'=>$customer_phone,
        'msg'=>$customer_message,
      ];

      if(empty($image) || $image == null)
      {
        $data['type'] = "text";
      }
      else
      {
        $data['img'] = $image;
        $data['type'] = "image";
      }

      // waweb api
      if($service == 0)
      {
        // LOGIC TO SEND MESSAGE
        $api = new Waweb;
        $ret = $api->send_message($phone_id,$customer_phone,$customer_message,$image);
        $ret['device_id'] = $phone_id;
        return $ret;
      }
      else if($service == 1)
      {
        $sending = self::send_message_wablas($data,$wablas_server);
      }
      else
      {
        // service  = 2
        $sending = self::send_wa_fonte_message($data);
      }

      //dd($sending);

      if($sending == null)
      {
         $ret = array(
          'status'=>3,
          'device_id'=>$phone_id
         );
         return $ret;
      }

      if(isset($sending['status']) == false)
      {
        $msg = str_replace(" ","_",$sending['message']);
        if($msg == "Please_Upgrade_Your_Account")
        {
          $ret = array(
            'status'=>2,
            'device_id'=>$phone_id
           );
          return $ret; //usually if user using package that not supported image / package run out
        }
        elseif($msg == "token_invalid")
        {
          $ret = array(
            'status'=>4,
            'device_id'=>$phone_id
           );
          return $ret; //invalid token
        }
        else
        {
          $ret = array(
            'status'=>3,
            'device_id'=>$phone_id
           );
          return $ret;
        }
      }
      else
      {
        $ret = array(
          'status'=>1,
          'device_id'=>$phone_id
         );
        return $ret;
      }
    }

  // WAFONTE SEND MESSAGE
  public static function send_wa_fonte_message($data)
    {
        $curl = curl_init();
        $token = $data['token'];

        if($data['type'] == 'text')
        {
            // text message
            $data = array(
                'phone' => $data['to'],
                'type' => $data['type'],
                'text' => $data['msg'],
                'delay' => '1',
                'schedule' => '0'
            );
        }
        else
        {
            // 'https://i5.walmartimages.com/asr/b3873509-e1e1-431b-9a98-9bd12d59bd72_1.3109eaf9d125b1b19ab961b4f6afe2b9.jpeg'
            $data = array(
                'phone' => $data['to'],
                'type' => $data['type'],
                'url' => $data['img'],
                'caption' => $data['msg'],
                'delay' => '1',
                'schedule' => '0'
            );
        }

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://md.fonnte.com/api/send_message.php",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $data,
            CURLOPT_HTTPHEADER => array(
                "Authorization: ".$token.""
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);
        $response = json_decode($response,true);
        return $response;
        // sleep(1); #do not delete!
    }

    // WABLAS SEND MESSAGE
    public static function send_message_wablas($data,$server)
    {
        $cs = new Custom;
        $curl = curl_init();
        $token = $data['token'];

        if($data['type'] == 'text')
        {
            // text message
            $data = [
              'phone' => $data['to'],
              'message' => $data['msg'],
              'isGroup' => 'true',
            ];
            $url = $cs::get_wablas()[$server]."/api/send-message";
        }
        else
        {
            // 'https://i5.walmartimages.com/asr/b3873509-e1e1-431b-9a98-9bd12d59bd72_1.3109eaf9d125b1b19ab961b4f6afe2b9.jpeg'
            $data = array(
                'phone' => $data['to'],
                'image' => $data['img'],
                'caption' => $data['msg'],
                'delay' => '1',
                'schedule' => '0'
            );
            $url = $cs::get_wablas()[$server]."/api/send-image";
        }

        curl_setopt($curl, CURLOPT_HTTPHEADER,
            array(
                "Authorization: $token",
            )
        );

        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);

        $result = curl_exec($curl);
        curl_close($curl);

        $res = json_decode($result,true);
        return $res;
    }

/* END CLASS */
}
