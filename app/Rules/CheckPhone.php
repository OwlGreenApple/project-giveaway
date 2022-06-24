<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Helpers\Custom;
use Illuminate\Support\Facades\Lang;

class CheckPhone implements Rule
{
    public $cond;

    public function __construct($cond)
    {
        $this->cond = $cond;
    }

    public function passes($attribute, $value)
    {
        $ct = new Custom;
        if($this->cond == 'phone')
        {
            if($value == null || empty($value))
            {
                return true;
            }

            if(!preg_match("/^[0-9]*$/i",$value))
            {
                return false;
            }
            else
            {
                return true;
            }
        }

        if($this->cond == 'service')
        {
            $number = [1,2];
            if(in_array($value,$number) == true)
            {
                return true;
            }
            else
            {
                return false;
            }
        }
        
        // wablas server array
        $number = $ct::get_wablas();
        if(isset($number[$value]))
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return Lang::get('cvalidation.check_user_phone');
    }
}
