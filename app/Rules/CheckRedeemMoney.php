<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Auth;
use App\Models\Redeem;
use App\Helpers\Custom;

class CheckRedeemMoney implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    private $msg;
    public function __construct()
    {

    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $hp = new Custom;
        $check = isset($hp::redeem()[$value]);

        if($check == false)
        {
            $this->msg = Lang::get('auth.redeem');
            return false;
        }
        else
        {
            // IN CASE USER REDEEM FOR THE FIRST TIME
            $redeem = Redeem::where('user_id',Auth::id())->first();
            if(is_null($redeem) && $hp::redeem()[$value] > $hp::redeem()[0])
            {
                $this->msg = Lang::get('auth.redeem.first').' '.$hp::currency()[Lang::get('auth.currency')].'-'.$hp::format($hp::redeem()[0]);
                return false;
            }

            // CHECK WHETHER FUND IS SUFFICIENT
            $funds = Auth::user()->money;
            if($funds < $hp::redeem()[$value])
            {
                $this->msg = Lang::get('auth.redeem.insufficient');
                return false;
            }

            // IN CASE IF USER TRY TO REDEEM BUT WITHDRAWAL STILL UNDER PROCESS
            $rdm = Redeem::where([['user_id',Auth::id()],['is_paid',0]])->first();
            if(!is_null($rdm))
            {
                $this->msg = Lang::get('auth.redeem.process');
                return false;
            }

            return true;
        }
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return $this->msg;
    }
}
