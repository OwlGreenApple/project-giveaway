<?php
namespace App\Helpers;
use App\Models\Entries;

class Custom
{
    public function get_price()
    {
        $price = [
            ['package'=>'free','price'=>0,'max_sell'=>30000,'max_trans'=>10000,'fee'=>15,'sell'=>1,'profit'=>0],
            ['package'=>'starter','price'=>100000,'max_sell'=>500000,'max_trans'=>50000,'fee'=>10,'sell'=>2,'profit'=>0.5],
            ['package'=>'doubler','price'=>200000,'max_sell'=>2000000,'max_trans'=>100000,'fee'=>10,'sell'=>3,'profit'=>0.5],
            ['package'=>'tripler','price'=>350000,'max_sell'=>3500000,'max_trans'=>150000,'fee'=>10,'sell'=>4,'profit'=>1],
            ['package'=>'quadrupler','price'=>500000,'max_sell'=>5000000,'max_trans'=>200000,'fee'=>10,'sell'=>5,'profit'=>1.5]
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
            $this->get_price()[4]['package'] => $this->get_price()[4]
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

    public function share_prize($show)
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

/* end of class */
}