<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Messages;
use App\Helpers\Custom;

class WABlasController extends Controller
{
    public function info()
    {
        $curl = curl_init();
        $token = "jENfX954myxHrWXOzF5eFzZbUETabu1vVbWZyLFNbIFRqkeyJvPCrIbFRsDxdepM";
        // $url = "https://wablas.com/generate/qr.php?token='".$token."'&url=aHR0cHM6Ly9jZXBvZ28ud2FibGFzLmNvbQ==";
        $url = "https://pati.wablas.com/api/device/info?token=".$token;

        curl_setopt($curl, CURLOPT_HTTPHEADER,
            array(
                "Authorization: $token",
            )
        );
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        $result = curl_exec($curl);
        curl_close($curl);

        echo "<pre>";
        print_r($result);
    }

    public function send_message(Request $req)
    {
        $curl = curl_init();
        $token = env('WA_TOKEN');
        $user = User::find($req->user_id);

        $to = $req->number;
        $message = $req->message;

        // SPONSOR MESSAGE
        $ct = new Custom;
        if($user->membership == 'free' || $user->membership == 'starter' || $user->membership == 'starter-yearly')
        {
            $message.= $ct::sponsor();
        }

        $data = [
            'phone' => $to,
            'message' => $message,
            'isGroup' => 'true',
        ];
        $url = "https://pati.wablas.com/api/send-message";
       
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

        //CUT QUOTA
        $user->counter_send_message_daily--;
        $user->save();

        $msg = Messages::find($req->msg_id);
        $msg->status = 2;
        $msg->wablas_id = $res['data']['messages'][0]['id'];
        $msg->save();
    }

/* end class */
}