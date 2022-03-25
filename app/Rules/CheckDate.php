<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Lang;
use Carbon\Carbon;
use App\Helpers\Custom;
use App\Models\Events;

class CheckDate implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public $cond;
    public $date;
    public $tmz;
    public $event_id;
    private $msg;

    public function __construct($cond,$date,$tmz = null,$event_id = 0)
    {
        $this->cond = $cond;
        $this->date = $date;
        $this->tmz = $tmz;
        $this->event_id = $event_id;
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
        return $this->check_time($value,$this->cond);
    }

    private function check_time($value,$terms)
    {
        if($terms == 'start')
        {
            $emsg = Lang::get('cvalidation.time.start');
            return $this->current_moment($value,$emsg,$this->tmz,$this->event_id);
        }

        if($terms == 'end')
        {
            $emsg = Lang::get('cvalidation.time.end');
            return $this->time_logic($value,$emsg);
        }
        
        if($terms == 'award')
        {
            $emsg = Lang::get('cvalidation.time.award');
            return $this->time_logic($value,$emsg);
        }

        if($terms == 'timezone')
        {
            $emsg = Lang::get('cvalidation.time.zone');
            return $this->time_zone($value,$emsg);
        }
    }

    private function current_moment($value,$emsg,$tmz,$event_id)
    {
        $ev = Events::find($event_id);

        if(!is_null($ev))
        {
            if($value == $ev->start)
            {
                return true;
            }
        }

        $choosendate = Carbon::parse($value)->toDateTimeString();
        if(Carbon::now($tmz)->gt($choosendate))
        {
            $this->msg = $emsg;
            return false;
        }
        else
        {
            return true;
        }
    }

    private function time_logic($value,$emsg)
    {
        $datetime = Carbon::parse($this->date);
        if(Carbon::parse($value)->lte($datetime))
        {
            $this->msg = $emsg;
            return false;
        }
        else
        {
            return true;
        }
    }

    private function time_zone($value,$emsg)
    {
        $helpers = new Custom;
        $timezone = array_keys($helpers::timezone());

        if(in_array($value,$timezone))
        {
            return true;
        }
        else
        {
            $this->msg = $emsg;
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
        return $this->msg;
    }
}
