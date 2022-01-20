<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Lang;
use App\Helpers\Custom;

class ProfileRules implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */

    public $cond;
    public $message;

    public function __construct($cond)
    {
        $this->cond = $cond;
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
        $custom = new Custom;
        $currency = $custom::currency();
        $lang = $custom::lang();

        if($this->cond == 'cur')
        {
            return $this->check_currency($currency,$value);
        }
        else
        {
            return $this->check_lang($lang,$value);
        }
    }

    private function check_currency($currency,$value)
    {
        $get_cur_key = array_keys($currency);
        if(in_array($value,$get_cur_key))
        {
            return true;
        }
        else
        {
            $this->message = Lang::get('cvalidation.cur');
            return false;
        }
    }

    private function check_lang($lang,$value)
    {
        $get_lang_key = array_keys($lang);
        if(in_array($value,$get_lang_key))
        {
            return true;
        }
        else
        {
            $this->message = Lang::get('cvalidation.lang');
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
        return $this->message;
    }
}
