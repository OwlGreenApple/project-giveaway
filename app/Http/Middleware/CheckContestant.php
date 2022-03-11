<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Rules\CheckValidPhone;

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
            'contestant'=>['required','max:30'],
            'email'=>['required','email'],
            'phone'=>['required','min:6', new CheckValidPhone($request->pcode)],
        ];

        $validator = Validator::make($request->all(),$rules);
        $err = $validator->errors();

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
}
