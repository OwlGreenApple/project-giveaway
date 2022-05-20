<?php
namespace App\Helpers;
use App\Models\Entries;
use App\Models\User;
use App\Models\Promo;
use App\Rules\CheckBannedEmail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Lang;

class Custom
{
    public function get_price()
    {
        $contestant_starter = 1000;
        $campaign_starter = 2;
        $wa_starter = 100;

        $contestant_gold = 2500;
        $campaign_gold = 3;
        $wa_gold = 200;

        $contestant_platinum = 5000;
        $campaign_platinum = 5;
        $wa_platinum = 300;

        $term_monthly = 1;
        $term_3_month = 3;
        $term_yearly = 12;

        $price = [
            ['package'=>'free','price'=>0,'terms'=>1,'contestants'=>100,'campaign'=>1,'discount'=>0,'wa'=>50],
            ['package'=>'starter','price'=>100000,'terms'=>$term_monthly,'contestants'=>$contestant_starter,'campaign'=>$campaign_starter,'wa'=>$wa_starter],
            ['package'=>'starter-3-month','price'=>85000,'terms'=>$term_3_month,'contestants'=>$contestant_starter,'campaign'=>$campaign_starter,'wa'=>$wa_starter,'discount'=>100000],
            ['package'=>'starter-yearly','price'=>60000,'terms'=>$term_yearly,'contestants'=>$contestant_starter,'campaign'=>$campaign_starter,'wa'=>$wa_starter,'discount'=>100000],
            ['package'=>'gold','price'=>250000,'terms'=>$term_monthly,'contestants'=>$contestant_gold,'campaign'=>$campaign_gold,'wa'=>$wa_gold],
            ['package'=>'gold-3-month','price'=>212500,'terms'=>$term_3_month,'contestants'=>$contestant_gold,'campaign'=>$campaign_gold,'wa'=>$wa_gold,'discount'=>200000],
            ['package'=>'gold-yearly','price'=>150000,'terms'=>$term_yearly,'contestants'=>$contestant_gold,'campaign'=>$campaign_gold,'wa'=>$wa_gold,'discount'=>200000],
            ['package'=>'platinum','price'=>350000,'terms'=>$term_monthly,'contestants'=>$contestant_platinum,'campaign'=>$campaign_platinum,'wa'=>$wa_platinum],
            ['package'=>'platinum-3-month','price'=>297500,'terms'=>$term_3_month,'contestants'=>$contestant_platinum,'campaign'=>$campaign_platinum,'wa'=>$wa_platinum,'discount'=>300000],
            ['package'=>'platinum-yearly','price'=>210000,'terms'=>$term_yearly,'contestants'=>$contestant_platinum,'campaign'=>$campaign_platinum,'wa'=>$wa_platinum,'discount'=>300000]
        ];

        return $price;
    }

    public function check_type($package)
    {
        $pack = [
            $this->get_price()[0]['package'] => $this->get_price()[0],
            $this->get_price()[1]['package'] => $this->get_price()[1],
            $this->get_price()[2]['package'] => $this->get_price()[2],
            $this->get_price()[3]['package'] => $this->get_price()[3],
            $this->get_price()[4]['package'] => $this->get_price()[4],
            $this->get_price()[5]['package'] => $this->get_price()[5],
            $this->get_price()[6]['package'] => $this->get_price()[6],
            $this->get_price()[7]['package'] => $this->get_price()[7],
            $this->get_price()[8]['package'] => $this->get_price()[8],
            $this->get_price()[9]['package'] => $this->get_price()[9]
        ];

        if(isset($pack[$package]))
        {
            return $pack[$package];
        }
        else
        {
            return false;
        }
    }

    public static function currency()
    {
        return ['usd'=>'USD','gbp'=>'GBP','sgd'=>'SGD','idr'=>'IDR','rm'=>'RM'];
    }

    public static function lang()
    {
        return ['en'=>'english','id'=>'bahasa'];
    }

    public static function redeem()
    {
        return [29000,195000,295000,395000,495000];
    }

    public static function convert_amount($amount)
    {
        $amount = str_replace(",","",$amount);
        return (int)$amount;
    }

    public static function format($val)
    {
        $val= number_format($val);
        return $val;
    }

    public static function timezone()
    {
        $timezone = [
            "Pacific/Auckland"=>"(UTC -11) Auckland",
            "Pacific/Tahiti"=>"(UTC -10) Papeete",
            "America/Anchorage"=>"(UTC -9) Anchorage",
            "America/Los_Angeles"=>"(UTC -8) San Francisco",
            "America/Denver"=>"(UTC -7) Salt Lake City",
            "America/Chicago"=>"(UTC -6) Dallas",
            "America/New_York"=>"(UTC -5) New York",
            "America/Guyana"=>"(UTC -4) Georgetown",
            "America/Sao_Paulo"=>"(UTC -3) Rio De Janeiro",
            "Atlantic/South_Georgia"=>"(UTC -2) King Edward Point",
            "Atlantic/Cape_Verde"=>"(UTC -1) Praia",
            "Europe/Dublin"=>"(UTC +0) Dublin",
            "Europe/Paris"=>"(UTC +1) Paris",
            "Europe/Helsinki"=>"(UTC +2) Helsinki",
            "Europe/Moscow"=>"(UTC +3) Moscow",
            "Asia/Dubai"=>"(UTC +4) Abu Dhabi",
            "Asia/Karachi"=>"(UTC +5) Islamabad",
            "Asia/Dhaka"=>"(UTC +6) Dhaka",
            "Asia/Jakarta"=>"(UTC +7) Jakarta/Bangkok",
            "Asia/Hong_Kong"=>"(UTC +8) Hong Kong",
            "Asia/Tokyo"=>"(UTC +9) Tokyo",
            "Australia/Brisbane"=>"(UTC +10) Cairns",
            "Pacific/Efate"=>"(UTC +11) Port Vila",
            "Asia/Anadyr"=>"(UTC +12) Anadyr"
        ];
        return $timezone;
    }

    //TO GIVE CIRCLE MARK IF CONTESTANT HAD DONE WITH BONUS ENTRIES
    public static function get_marks($bonus_id,$event_id,$type,$contestant_id,$promo = null,$cond = 0)
    {
        if($promo == null)
        {
            $logic = [
                ['bonus_id',$bonus_id],
                ['event_id',$event_id],
                ['type',$type],
                ['contestant_id',$contestant_id],
            ];

            $db = Entries::where($logic)->first();
        }
        else
        {
            $db = Promo::where([['event_id',$event_id],[$type,$cond]])->first();
        }

        if(is_null($db))
        {
            $icon = '<i class="far fa-circle"></i>';
        }
        else
        {
            $icon = '<i class="fas fa-check-circle main-color"></i>';
        }

        return $icon;
    }

    public static function share_prize($show)
    {
        if($show == null)
        {
            $prize = "+3";
        }
        else
        {
            $prize = 3;
        }

        return $prize;
    }

    public static function sponsor($html = null)
    {
        $msg = '';

        if($html == null)
        {
            $msg .= "\n\n".'Powered by topleads.app';
        }
        else
        {
            $msg .= 'Powered by <a class="main-color" href="https://topleads.app">'.env('APP_NAME').'</a>';
        }

        return $msg;
    }

    // CHECK BOUNCING EMAIL
    public function check_email_bouncing($email,$cond = null)
    {
        $user = User::where('email',$email)->first();
        $check = new CheckBannedEmail;

        //  in case contact us
        if($cond == 'admin')
        {
            return true;
        }

        // PASS IF EMAIL  = 1 || 3
        if($cond == null)
        {
            if(is_null($user) || $user->is_valid_email == 3)
            {
                return false;
            }

            if($user->is_valid_email == 1)
            {
                return true;
            }
        }

        if($check::check_bouncing($email) == true)
        {
            if($cond == "new")
            {
                return 1;
            }

            $is_valid_email = 1;
            $status = true;
        }
        elseif($check::check_bouncing($email) == "empty")
        {
            if($cond == "new")
            {
                return 2;
            }
            $is_valid_email = 2;
            $status = false;
        }
        else
        {
            if($cond == "new")
            {
                return 3;
            }
            $is_valid_email = 3;
            $status = false;
        }

        // REGISTERED USER
        if(!is_null($user))
        {
            $user_id = $user->id; 
            $usr = User::find($user_id);
            $usr->is_valid_email = $is_valid_email;
            $usr->save();
        }
    
        return $status;
    }

    public function mail($email,$obj,$cond)
    {
        if($this->check_email_bouncing($email,$cond) == true)
        {
            Mail::to($email)->send($obj);
            return true;
        }
        else
        {
            return false;
        }
    }

    // SETUP FOR WA
    public static function forgot($password,$name)
    {
      $msg ='';
      $msg .=Lang::get('email.hi').' '.$name.','."\n\n";
      $msg .=Lang::get('email.reset').''."\n";
      $msg .='*'.$password.'*'."\n\n";
      $msg .=Lang::get('email.help.if')."\n";
      $msg .='*'.Lang::get('email.help.contact').'*'."\n";
      $msg .='Telegram : @activomni_cs'."\n\n";
      $msg .=Lang::get('email.thank')."\n";
      $msg .='Team'.env('APP_NAME');

      return $msg;
    }

/* end of class */
}
