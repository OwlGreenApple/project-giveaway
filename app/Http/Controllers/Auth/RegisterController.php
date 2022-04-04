<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Auth\ForgotPasswordController AS FG;
use App\Providers\RouteServiceProvider;
use App\Models\User;
use App\Helpers\Custom;
use App\Mail\RegisteredEmail;
use App\Rules\CheckBannedEmail;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Carbon\Carbon;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */

    protected function create(array $data)
    {
        $generated_password = substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789'),0,10);
        $membership = 'free';
        $end_membership = null;

        $helper = new Custom;
        $check = $helper->check_email_bouncing(strip_tags($data['email']),"new");
        if($check == 3)
        {
          return 'imail';
        }

        $col = [
          'name' => strip_tags($data['username']),
          'email' => strip_tags($data['email']),
          'password' => Hash::make($generated_password),
          'membership'=>$membership,
          'end_membership'=>$end_membership,
          'myreferral'=>strip_tags($data['myreferral']),
          'is_valid_email'=>$check,
        ];

        // PREMIUM MEMBERSHIP
        if(isset($data['membership']))
        {
            $col['membership'] = strip_tags($data['membership']);
        }

        // CASE WA
        // $msg = new Custom;
        // $msg = $msg::registered($generated_password,strip_tags($data['username']));
        $msg = null;

        $data = [
        //   'message'=>$msg,
        //   'phone_number'=>$phone_number,
          'email'=>$data['email'],
          'obj'=>new RegisteredEmail($generated_password,strip_tags($data['username'])),
        ];

        $adm = new FG;
        $adm->notify_user($data);

        Cookie::queue(Cookie::forget('referral_code'));
        return User::create($col);
    }

    protected function ajax_validator(array $data)
    {
        $validator = Validator::make($data, [
            'username' => ['required','string','min:4','max:30'],
            'email' => ['required','string', 'email', 'max:60', 'unique:users', new CheckBannedEmail],
            //'code_country' => ['required',new CheckPlusCode,new CheckCallCode],
            //'phone' => ['required','numeric','digits_between:6,18',new InternationalTel,new CheckUserPhone($data['code_country'],null), new CheckUniquePhone($data['code_country'],$data['phone'])]
        ]);

        $err = $validator->errors();
        if($validator->fails() == true)
        {
            $errors = [
              'success'=>0,
              'username'=>$err->first('username'),
              'email'=>$err->first('email'),
            //   'code_country'=>$err->first('code_country'),
            //   'phone'=>$err->first('phone'),
            ];
            return response()->json($errors);
        }

        return $this->register_ajax($data);
    }

    // public function register_redirect()
    // {
    //   $request = new Request(Session::get('reg'));
    //   return $this->register($request);
    // }

    public function register(Request $request)
    {
        $req = $request->all();

        //read cookie referral code
        $referral_code = $request->cookie('referral_code');
        $req['myreferral'] = 0;

        if($referral_code !== null)
        {
            $user_referral = User::where('referral_code',$referral_code)->first();
            if (!is_null($user_referral))
            {
                $req['myreferral'] = $user_referral->id;
            }
        }

        if($request->ajax() == true)
        {
          return $this->ajax_validator($req,$request);
        }
        // else
        // {
        //     $signup = $this->create($req);
        //     $order = null;
        //     Auth::loginUsingId($signup->id);

        //     return redirect('home');
        // }
    }

    //REGISTER VIA AJAX
    protected function register_ajax(array $data)
    {
        $signup = $this->create($data);

        // IF EMAIL VALIDATOR == 3 / INVALID
        if($signup == 'imail')
        {
          return response()->json([
            'success' => 0,
            'email' => Lang::get('auth.imail')
          ]);
        }

        Auth::loginUsingId($signup->id);
        return response()->json([
            'success' => 1,
            'email' => $signup->email,
        ]);
    }

    public function price_page($referral_code=null)
    {
      $minutes = 60*24*7; // 7 days

      // set cookie for referal
      if($referral_code !== null)
      {
        Cookie::queue(Cookie::make('referral_code',$referral_code, $minutes));
      }

      return view('package',['pc'=> new Custom,'cond'=>true,'account'=>0]);
    }

/* end controller */
}
