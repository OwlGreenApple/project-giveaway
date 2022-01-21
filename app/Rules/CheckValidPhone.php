<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Lang;

class CheckValidPhone implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */

    public $code;
    public function __construct($code)
    {
        $this->code = $code;
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
        $code = $this->code;
        if(preg_match("/^\+[0-9]/i",$code) && is_numeric($value) == true)
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
        return Lang::get('cvalidation.phone');
    }
}
