<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use App\Mail\RegisteredEmail;
use App\Models\User;
use App\Helpers\Custom;

class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    use SendsPasswordResetEmails;

     // SEND PASSWORD TO FORGOT EMAIL
     public function reset(Request $request)
     {
       $email = strip_tags($request->email);

       $validator = Validator::make($request->all(), [
         'email' => 'required|email|max:60',
       ]);

       if($validator->fails() == true)
       {
         return redirect()->back()->withErrors($validator)->withInput();
       }

       $user = User::where('email',$email)->first();

       if(is_null($user))
       {
         return redirect('password/reset')->with('error_email',Lang::get('auth.failed'));
       }

       $banned_user = User::where([['email',$email],['status','>',0]])->first();

       if(is_null($banned_user))
       {
         return redirect('password/reset')->with('error_email',Lang::get('auth.banned'));
       }

       $generated_password = substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789'),0,10);
       $u_user = User::find($user->id);
       $u_user->password = Hash::make($generated_password);

       try
       {
         $u_user->save();
        //  WA CASE
        //  $msg = new Custom;
        //  $msg = $msg::forgot($generated_password,$user->username);
         $msg = null;

         self::send_notify($user->id,$msg,new RegisteredEmail($generated_password,$user->username,'forgot'));
         return redirect('password/reset')->with('status',Lang::get('auth.success'));
       }
       catch(QueryException $e)
       {
         return redirect('password/reset')->with('error_email',Lang::get('custom.failed'));
       }
     }

     private static function send_notify($user_id,$msg,$email)
    {
      $user = User::find($user_id);
      $data = [
        // 'message'=>$msg,
        // 'phone_number'=>$user->phone_number,
        'email'=>$user->email,
        'obj'=>$email,
      ];

      self::notify_user($data);
    }

    // GLOBAL NOTIFCATION USER THROUGH wa AND EMAIL
    public static function notify_user(array $data)
    {
    //   $notif = Notification::all()->first();
    //   $admin_id = $notif->admin_id;
    //   $api = new Api;

    //   $api->send_wa_message($admin_id,$data['message'],$data['phone_number']);
      Mail::to($data['email'])->send($data['obj']);
    }

/* end class */
}
