<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Lang;
use App\Rules\CheckValidPhone;
use App\Models\Contestants;
use App\Models\User;
use App\Models\Events;
use App\Models\EmailReg;
use App\Models\Ipblock;
use App\Helpers\Custom;
use App\Console\Commands\RunningMessages as CMD;
use App\Rules\CheckBannedEmail;
use Carbon\Carbon;

class CheckContestant
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */

    public function handle(Request $request, Closure $next)
    {
        // return self::note_ip_block(); 
        $mailcheck = new CheckBannedEmail;
        $rules = [
            'contestant'=>['required','min:3','max:30'],
            'email'=>['bail','required','email',$mailcheck], 
            'phone'=>['required','min:6', new CheckValidPhone($request->pcode)],
        ];

        $email = strip_tags($request->email);
        $err_mail = ['ebullshit'=>Lang::get('auth.imail').'.'];
        $validator = Validator::make($request->all(),$rules);
        $err = $validator->errors();

        // CHECK MAX CONTESTANT ACCORDING ON PACKAGE
        $event = Events::where('url_link',$request->link)->first();

        if(is_null($event))
        {
            return view('error404');
        }

        $user = User::find($event->user_id);
        $total_contestant = Contestants::where([['contestants.event_id',$event->id],['events.user_id',$user->id]])
                            ->join('events','events.id','=','contestants.event_id')->get()->count();
        $ct = self::check_contestants_membership($user,$total_contestant);

        // validation contestant form
        if($validator->fails() == true)
        {
            $errors = [
                'err'=>1,
                0 =>[$err->first('contestant'),'contestant'],
                1 =>[$err->first('email'),'email'],
                2 =>[$err->first('phone'),'phone'],
            ];

            return response()->json($errors);
        }

        // VALIDATION ACCORDING ON DATABASE reg_email
        $check_reg_email = self::note_contestant_email($email,'filter');
        if($check_reg_email == false)
        {
            return response()->json($err_mail);
        }

        // if is_error = 0 or the email was passed
        if($check_reg_email == 'no-error')
        {
            return $next($request);
        }

        // check counted ip in 1 day if day same and counted equal = 3 then validate
        $check_ip = self::note_ip_block(1);
        if($check_ip == false)
        {
            return response()->json($err_mail);
        }

        // prevent user to put bullshit email
        if($mailcheck::check_bouncing($email) == false)
        {
            self::note_contestant_email($email,1); //save to email reg
            self::note_ip_block(); //save to ipblock
            return response()->json($err_mail);
        }

        if($mailcheck::check_bouncing($email) == true) 
        {
            // in case if email is passed
            self::note_contestant_email($email,0);
            
        }
        else
        {   // in case if bulkemail quota is empty
            self::note_contestant_email($email,2);
        }

        // validation if contestant full
        if($ct === false)
        {
            if($user->membership == 'free')
            {
                $max_message = '<div class="alert alert-warning text-center">'.Lang::get('custom.fcontestants.free').' <b> '.$event->admin_contact.'</b></div>';
            }
            else
            {
                $max_message = '<div class="alert alert-warning text-center">'.Lang::get('custom.fcontestants').'</div>';
            }

            // logic send wa
            $wa_msg = Lang::get('email.max_contestant');

            $wa = new CMD;
            $msge = [
                'user_id'=>$user->id,
                'ev_id'=>$event->id, 
                'bc_id'=>0, 
                'ct_id'=>0,
                'sender'=>env('WA_TEMP'),
                'receiver'=>substr($event->admin_contact,1),
                'message'=>$wa_msg,
                'img_url'=>null
            ];
            $wa::ins_message($msge);
            
            $rt['nmax'] = $max_message;
            return response()->json($rt);
        }

        return $next($request);
    }

    // Function to get the client IP address
    public static function get_client_ip() {
        $ipaddress = '';
        if (isset($_SERVER['HTTP_CLIENT_IP']))
            $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
        else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
            $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
        else if(isset($_SERVER['HTTP_X_FORWARDED']))
            $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
        else if(isset($_SERVER['HTTP_FORWARDED_FOR']))
            $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
        else if(isset($_SERVER['HTTP_FORWARDED']))
            $ipaddress = $_SERVER['HTTP_FORWARDED'];
        else if(isset($_SERVER['REMOTE_ADDR']))
            $ipaddress = $_SERVER['REMOTE_ADDR'];
        else
            $ipaddress = 'UNKNOWN';
        return $ipaddress;
    }

    //  save every contestant email on email reg
    public static function note_contestant_email($email,$status = 0)
    {
        $em = EmailReg::where('email',$email)->first();

        if($status == 'filter')
        {
            if(is_null($em))
            {
                return true;
            }

            // in case filter and no-error
            if($em->is_error == 0)
            {
                return 'no-error';
            }

            if($em->is_error == 1)
            {
                return false;
            }
        }

        if(is_null($em))
        {
            $emreg = new EmailReg;
            $emreg->email = $email;
            $emreg->is_error = $status;
            $emreg->save();
        }
        elseif($filter == 1)
        {

        }
        else
        {
            $emg = EmailReg::find($em->id);
            $emg->is_error = $status;
            $emg->save();
        }
    }

    //  save every contestant ip block
    public static function note_ip_block($filter = null)
    {
        $ipaddress = self::get_client_ip();
        $ip = Ipblock::where('ip',$ipaddress)->first();

        if(is_null($ip) && $filter == null)
        {
            $ipb = new Ipblock;
            $ipb->ip = $ipaddress;
            $ipb->counted = 1;
            $ipb->save();
        }
        elseif(is_null($ip) && $filter !== null)
        {
            return true;
        }
        else
        {
            $ipf = Ipblock::find($ip->id);
            $day = Carbon::parse($ipf->updated_at)->toDateString();

            // in case to check if total count greater equal than 3
            if($ipf->counted >= 3 && Carbon::parse($day)->eq(Carbon::now()->toDateString()) )
            {
                return false;
            }

            // to prevent counted increase if filter = 1 
            if($filter !== null)
            {
                return true;
            }

            //  if new day would reset counted to 1
            if(Carbon::parse($day)->lt(Carbon::now()->toDateString()))
            {
                $ipf->counted = 1;
            }
            else
            {
                $ipf->counted++;
            }
        
            $ipf->save();
        }
        return true;
    }

    private static function check_contestants_membership($user,$total_contestant)
    {
        $ct = new Custom;
        $max_contestants = $ct->check_type($user->membership)['contestants'];
        if($total_contestant >= $max_contestants)
        {
            return false;
        }

        return true;
    }


/* end class */
}
