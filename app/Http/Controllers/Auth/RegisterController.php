<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\Models\User;
use App\Helpers\Custom;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Auth;
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

        $col = [
          'name' => strip_tags($data['username']),
          'email' => strip_tags($data['email']),
          'password' => Hash::make($generated_password),
          'membership'=>$membership,
          'end_membership'=>$end_membership,
          'myreferral'=>strip_tags($data['myreferral']),
        ];

        // PREMIUM MEMBERSHIP
        if(isset($data['membership']))
        {
            $col['membership'] = strip_tags($data['membership']);
        }

        // $msg = new Messages;
        // $msg = $msg::registered($generated_password,strip_tags($data['username']));

        // $data = [
        //   'message'=>$msg,
        //   'phone_number'=>$phone_number,
        //   'email'=>$data['email'],
        //   'obj'=>new RegisteredEmail($generated_password,strip_tags($data['username'])),
        // ];

        // $adm = new adm;
        // $adm->notify_user($data);

        return User::create($col);
    }

    protected function ajax_validator(array $data)
    {
        $validator = Validator::make($data, [
            'username' => ['required','string','min:4','max:30'],
            'email' => ['required','string', 'email', 'max:60', 'unique:users'],
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

    public function register_redirect()
    {
      $request = new Request(Session::get('reg'));
      return $this->register($request);
    }

    public function register(Request $request)
    {
        $req = $request->all();

        //read cookie referral code
        $referral_code = $request->cookie('referral_code');
        $user_referral = User::where('referral_code',$referral_code)->first();
        if (!is_null($user_referral)) {
          $req['myreferral'] = $user_referral->id;
        }

        if($request->ajax() == true)
        {
          return $this->ajax_validator($req,$request);
        }
        else
        {
            $signup = $this->create($req);
            $order = null;
            Auth::loginUsingId($signup->id);

            return redirect('home');
        }
    }

    //REGISTER VIA AJAX
    protected function register_ajax(array $data)
    {
        $signup = $this->create($data);
        $order = null;

        Auth::loginUsingId($signup->id);
        return response()->json([
            'success' => 1,
            'email' => $signup->email,
        ]);
    }

    public function price_page($referral_code=null)
    {
      $minutes = 60*24*7; // 7 days
      $response = new Response('Set Cookie');
      $response->withCookie(cookie('referral_code', $referral_code, $minutes));
      // return $response;
      return view('package',['pc'=> new Custom,'cond'=>true,'account'=>0]);
    }

/* end controller */
}
