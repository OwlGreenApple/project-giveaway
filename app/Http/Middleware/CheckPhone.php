<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Rules\CheckPhone AS CPH;

class CheckPhone
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
            'service'=>[new CPH('service')],
            'wablas'=>[new CPH('wablas')],
            'phone'=>[new CPH('phone')],
            'api_key'=>['required_with:phone','max:255'],
        ];

        $validator = Validator::make($request->all(),$rules);

        if($validator->fails() == true)
        {
            $err = $validator->errors();
            $errors = [
                'success'=>'err',
                'service'=>$err->first('service'),
                'wablas'=>$err->first('wablas'),
                'phone'=>$err->first('phone'),
                'api_key'=>$err->first('api_key'),
            ];

            return response()->json($errors);
        }

        return $next($request);
    }
}