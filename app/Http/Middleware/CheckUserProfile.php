<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Rules\ProfileRules;

class CheckUserProfile
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
            'profile_name'=>['required','max:50'],
            'profile_lang'=>['required',new ProfileRules('lang')],
        ];

        if($request->password !== null)
        {
            $rules['password'] = ['min:8','max:50','confirmed'];
        }

        $validator = Validator::make($request->all(),$rules);
        $err = $validator->errors();

        if($validator->fails() == true)
        {
            $errors = [
                'success'=>'err',
                0=>[$err->first('profile_name'),'profile_name'],
                1=>[$err->first('password'),'password'],
                2=>[$err->first('profile_currency'),'profile_currency'],
                3=>[$err->first('profile_lang'),'profile_lang'],
            ];

            return response()->json($errors);
        }

        return $next($request);
    }
}
