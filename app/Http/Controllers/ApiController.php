<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApiController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    //DISPLAY ACTIVRESPON LIST
  public function get_activrespon_lists()
  {
    // $url = "https://192.168.0.114/activrespons/display_api_list";
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
    $url = "https://192.168.0.114/activrespons/save_customer";
    // $url = "https://activrespon.com/dashboard/save_customer";
    $user = Auth::user();
   
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


/* end class */
}
