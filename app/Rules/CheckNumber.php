<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Lang;
use App\Helpers\Custom;

class CheckNumber implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */

    public $cond;
    private $msg;

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
        if($this->cond == null)
        {
            return $this->max_prize_value($value);
        }
        elseif($this->cond == 'img')
        {
            return $this->check_image($value);
        }
    }

    private function max_prize_value($value)
    {
        $helper = new Custom;
        $prize = $helper::convert_amount($value);

        if($prize > 1000000)
        {
            $this->msg = Lang::get('cvalidation.prize');
            return false;
        }
        else
        {
            return true;
        }
    }

    private function check_image($value)
    {
        dd($value);
        $val = count($value);
        if($val < 1)
        {
            $this->msg = Lang::get('cvalidation.img');
            return false;
        }
        else
        {
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
