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
use App\Helpers\Custom;
use App\Console\Commands\RunningMessages as CMD;

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
        $rules = [
            'contestant'=>['required','min:3','max:30'],
            'email'=>['required','email'],
            'phone'=>['required','min:6', new CheckValidPhone($request->pcode)],
        ];

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

        return $next($request);
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
