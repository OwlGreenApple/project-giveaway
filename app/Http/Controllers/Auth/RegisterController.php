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
          'myreferral'=>0
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

    public function price_page()
    {
      return view('package',['pc'=> new Custom,'cond'=>true,'account'=>0]);
    }

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

    //   try
    //   {
    //     $u_user->save();
    //     $msg = new Messages;
    //     $msg = $msg::forgot($generated_password,$user->username);

    //     self::send_notify($user->id,$msg,new RegisteredEmail($generated_password,$user->username,'forgot'));
    //     return redirect('password/reset')->with('status',Lang::get('auth.success'));
    //   }
    //   catch(QueryException $e)
    //   {
    //     return redirect('password/reset')->with('error_email',Lang::get('custom.failed'));
    //   }
    }

/* end controller */     
}
