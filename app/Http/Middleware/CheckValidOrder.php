<?php

namespace App\Http\Middleware;

use App\Rules\CheckPricing;
use Illuminate\Support\Facades\Validator;
use Closure,Session;
use Carbon\Carbon;

class CheckValidOrder
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // checkout from summary
        if($request->summary == true)
        {
            $req = ['package'=>Session('order')['package']];
        }
        else
        {
            $req = $request->all();
        }

        //dd($req);
        //  ---- temp for digimaru until dec 1 2022
        if($request->package == 'starter-special')
        {
            $today = Carbon::now();
            $valid_date = Carbon::parse('12/1/2022')->toDateString();

            if($today->gte($valid_date))
            {
                $errors = array(
                    'status'=>0,
                    'package'=>'Paket tidak valid',
                );
    
                return response()->json($errors);
            }
        }
        //  ---- end temp for digimaru until dec 1 2022
        
        $rules = [
            'package'=>[new CheckPricing]
        ];

        $validator = Validator::make($req,$rules);
        
        if($validator->fails() == true)
        {
            $error = $validator->errors();
            $errors = array(
                'status'=>0,
                'package'=>$error->first('package'),
            );

            return response()->json($errors);
        }

        return $next($request);
    }
}
