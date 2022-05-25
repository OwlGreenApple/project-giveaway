<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use MailchimpMarketing as MC;
use App\Models\User;
use GuzzleHttp;

class ApiController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    //DISPLAY ACTIVRESPON LIST
  public function get_activrespon_lists()
  {
    // $url = "https://192.168.100.80/activrespon/display_api_list";
    $url = "https://activrespon.com/dashboard/display_api_list";

    $user = Auth::user();
    $data = array(
      "service" => '$2y$10$JMoAeSl6aV0JCHmTNNafTOuNlMg/S7Yo8a6LUauEZe4Rcy.YdU37S',
      "api_key_list" => $user->activrespon_api,
    );

    $data_string = json_encode($data);
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_VERBOSE, 0);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0);
    curl_setopt($ch, CURLOPT_TIMEOUT, 360);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Content-Type: application/json'
    ));

    $res=curl_exec($ch);

    // dd($res);
    return json_decode($res,true);
  }

  public function save_to_activrespon_lists(array $data)
  {
    // $url = "https://192.168.0.114/activrespons/save_customer";
    $url = "https://activrespon.com/dashboard/save_customer";
    $user = User::find($data['user_id']);

    $data = array(
      "service" => '$2y$10$JMoAeSl6aV0JCHmTNNafTOuNlMg/S7Yo8a6LUauEZe4Rcy.YdU37S',
      "api_key_list" => $user->activrespon_api,
      "name" => $data['api_name'],
      "email" => $data['api_email'],
      "phone" => $data['api_phone'],
      "list_id" => $data['list_id'],
    );

    $data_string = json_encode($data);
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_VERBOSE, 0);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0);
    curl_setopt($ch, CURLOPT_TIMEOUT, 360);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Content-Type: application/json'
    ));

    $res=curl_exec($ch);

    // dd($res);
    return $res;
  }

  //MAILCHIMP
  private static function mailchimp($user_id = null)
  {
    if($user_id == null)
    {
      $api_key = Auth::user()->mailchimp_api;
    }
    else
    {
      $user = User::find($user_id);
      $api_key = $user->mailchimp_api;
    }

    if($api_key == null)
    {
      return false;
    }

    $exp = explode("-",$api_key);
    $server = $exp[1];
    $mailchimp = new MC\ApiClient();

    $mailchimp->setConfig([
      'apiKey' => $api_key,
      'server' => $server
    ]);

    return $mailchimp;
  }


  public function mailchimp_valid_api()
  //$api_key,$server_mailchimp,$audience_id
  {
    $mailchimp = self::mailchimp();
    try
    {
        $mailchimp->ping->get();
        return true;
    }
    catch(GuzzleHttp\Exception\ConnectException $e)
    {
        return false;
    }
    catch(GuzzleHttp\Exception\ClientException $e)
    {
        return false;
    }
    // $response = $mailchimp->lists->getAllLists();
  }

  public function display_mailchimp_lists()
  {
    $mailchimp = self::mailchimp();

    if($mailchimp == false)
    {
      return array();
    }
    return $mailchimp->lists->getAllLists()->lists;
  }

  //TO ADD CONTACTS / SUBSCRIBER INTO AUDIENCE/LIST ON MAILCHIMP
  public function add_mailchimp(array $data)
  {
    $mailchimp = self::mailchimp($data['user_id']);

    $list_id = strip_tags($data['list_id']);
    $email = strip_tags($data['email']);
    $fname = strip_tags($data['name']);

    try {
        $mailchimp->lists->addListMember($list_id, [
          "email_address" => $email,
          "status" => "subscribed",
          "merge_fields" => [
            "FNAME" => $fname,
          ]
      ]);

      $err['success'] = 1;
    } catch (GuzzleHttp\Exception\ClientException $e) {
      $error = $e->getResponse()->getBody()->getContents();
      $err = json_decode($error,true); //$err['detail']
      $err['success'] = 0;
    }
    return $err['success'];
  }

  /* SENDFOX */
  public function display_sendfox_lists()
  {
    $url = "https://api.sendfox.com/lists";

    $user = Auth::user();
    $token = $user->sendfox_api;

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
    'authorization: Bearer '.$token
    ));

    $res=curl_exec($ch);

    // dd($res);
    return json_decode($res,true);
  }

  public function saveSendFox($email,$first_name,$last_name,$list,$user_id)
    {
        $user = User::find($user_id);
        $token = $user->sendfox_api;

        $data = array(
            'email'=>$email,
            'first_name'=>$first_name,
            'last_name'=>$last_name,
            'lists'=> $list
        );

        $url = 'https://api.sendfox.com/contacts';
        $data_string = json_encode($data);
        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_VERBOSE, 0);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0);
        curl_setopt($ch, CURLOPT_TIMEOUT, 360);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'authorization: Bearer '.$token
        ));
       
        $response = curl_exec($ch);
        $err = curl_error($ch);
        curl_close($ch);

        if ($err) {
          echo "cURL Error #:" . $err;
        } else {
          json_decode($response,true);
        }
    }

/* end class */
}
