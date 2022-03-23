<?php
namespace App\Helpers;
use App\Models\Entries;

class Custom
{
    public function get_price()
    {
        $price = [
            ['package'=>'free','price'=>0,'terms'=>1,'contestants'=>100,'campaign'=>1,'discount'=>0,'wa'=>50],
            ['package'=>'starter','price'=>100000,'terms'=>1,'contestants'=>1000,'campaign'=>2,'wa'=>100],
            ['package'=>'starter-yearly','price'=>1200000,'terms'=>12,'contestants'=>1000,'campaign'=>2,'wa'=>100,'discount'=>100000],
            ['package'=>'gold','price'=>250000,'terms'=>1,'contestants'=>2500,'campaign'=>3,'wa'=>200],
            ['package'=>'gold-yearly','price'=>2400000,'terms'=>12,'contestants'=>2500,'campaign'=>3,'wa'=>200,'discount'=>200000],
            ['package'=>'platinum','price'=>350000,'terms'=>1,'contestants'=>5000,'campaign'=>5,'wa'=>300],
            ['package'=>'platinum-yearly','price'=>3600000,'terms'=>12,'contestants'=>5000,'campaign'=>5,'wa'=>300,'discount'=>300000]
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
            $this->get_price()[6]['package'] => $this->get_price()[6]
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
        return ['usd'=>'USD','idr'=>'IDR'];
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
    public static function get_marks($bonus_id,$event_id,$type,$contestant_id)
    {
        $logic = [
            ['bonus_id',$bonus_id],
            ['event_id',$event_id],
            ['type',$type],
            ['contestant_id',$contestant_id],
        ];

        $et = Entries::where($logic)->first();

        if(is_null($et))
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

    public static function forgot($password,$name)
    {
      $msg ='';
      $msg .='Halo '.$name.','."\n\n";
      $msg .='You have reset your password already, here\'s your new passowrd :'."\n";
      $msg .='*'.$password.'*'."\n\n";
      $msg .='If you need assistance'."\n";
      $msg .='*Please contact our customer service*'."\n";
      $msg .='Telegram : @activomni_cs'."\n\n";
      $msg .='Thank you'."\n";
      $msg .='Team'.env('APP_NAME');

      return $msg;
    }

/* end of class */
}
