<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Redeem;
use App\Models\User;
use App\Rules\CheckRedeemMoney;

class CheckRedeem
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
            'account'=>['required','max:100'],
            'number'=>['required','numeric','digits_between:2,30'],
            'confirm'=>['required','same:number'],
            'amount'=>['required', new CheckRedeemMoney]
        ];

        $vd = Validator::make($request->all(),$rules);
        $err = $vd->errors();

        if($vd->fails() == true)
        {
            $errors = [
                'success'=>'err',
                'account'=>$err->first('account'),
                'number'=>$err->first('number'),
                'amount'=>$err->first('amount'),
                'confirm'=>str_replace(['number','confirm'],['Dana account number','Confirm Dana account number'],$err->first('confirm'))
            ];

            return response()->json($errors);
        }

        return $next($request);
    }
}
