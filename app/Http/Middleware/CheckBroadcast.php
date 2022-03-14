<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Auth;
use App\Rules\CheckDate;
use App\Rules\CheckMessage;
use App\Models\Events;
use App\Helpers\Custom;

class CheckBroadcast
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
            'title'=>['required','max:30'],
            'message'=>['bail','required','max:65000',new CheckMessage],
            'media'=>['bail','mimes:jpeg,jpg,png','max:1024'],
            'date_send'=>['required'],
            'timezone'=>['required', new CheckDate('timezone',null)],
        ];

        $validator = Validator::make($request->all(),$rules);
        $err = $validator->errors();
        $errors = $total_ct = array();
        $rct = true;

        if($request->ct_id == null)
        {
            $ctid_err = Lang::get('custom.ct');
            $rct = false;
        }
        else
        {
            $ctid_err = '';
            $total_ct = count($request->ct_id);

            // PREVENT MAX CONTESTANT GREATER THAN 10 WHEN USER TRYNG TO HACK
            if($total_ct > 10)
            {
                $ctid_err = Lang::get('custom.ct.max');
                $rct = false;
            }
        }

        if($validator->fails() == true || $rct == false)
        {
            $errors = [
                'success'=>'err',
                'title'=>$err->first('title'),
                'message'=>$err->first('message'),
                'media'=>$err->first('media'),
                'date_send'=>$err->first('date_send'),
                'timezone'=>$err->first('timezone'),
                'ct_id'=>$ctid_err,
            ];

            return response()->json($errors);
        }

        return $next($request);
    }
}
