<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Lang;

class CheckBannedEmail implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
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
        // CHECK BALCKLISTED EMAIL
        $banned = ["driely","alotivi","detectu","dhamsi","unicobd","cream","ahk","magim","owleyes","fuwamofu","ruru","moimoi","eay","honeys","eay","via","hamham","ichigo","mirai","f5","stayhome","usako","effexts","jmvoice","dhnow","jincer","rwstatus","nicloo","gonaute","intobx","yusolar","tirillo","mofpay","oxtenda","onymi","novstan","trynta","azqas","coinxt","mailfm","wwc8","rwstatus","toolve","fabtivia","unite5","mailvs","anidaw","boxnavi","ostinmail","93re","ayfoto","rilemei","xredb","aituvip","doulas","cutsup","techtary","itcess"];
        $mail = explode("@", $value);
        $banned_link = explode(".",$mail[1]);

        if(preg_match("/[\.]/i",$mail[1]) == 0)
        {
            return false;
        }

        if(in_array($banned_link[0],$banned))
        {
            return false;
        }
        else
        {
            return true;
        }

    }

    // TO USE AS VALIDATOR TO AVOID DOUBLE CHECK VALIDATE EMAIL
    public static function check_bouncing($mail)
    {
        // set the api key and email to be validated
        $key = '64GkX3EK7xRBADyYw2htLbnpvmJI8quH';
        $email = urlencode($mail);

        // use curl to make the request
        $url = 'https://api-v4.bulkemailchecker.com/?key='.$key.'&email='.$email;
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15); 
        curl_setopt($ch, CURLOPT_TIMEOUT, 15); 
        $response = curl_exec($ch);
        curl_close($ch);

        // decode the json response
        $json = json_decode($response, true);

        // if address is failed, alert the user they entered an invalid email
        if(isset($json['status']) && $json['status'] == "failed")
        {
            return false;
        }
        elseif(isset($json['hourlyQuotaRemaining']) && $json['hourlyQuotaRemaining'] < 1)
        {
            return 'empty';
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
        return Lang::get('auth.imail');
    }
}
