<?php
namespace App\Helpers;

class Custom
{
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
            "Asia/Bangkok"=>"(UTC +7) Bangkok",
            "Asia/Hong_Kong"=>"(UTC +8) Hong Kong",
            "Asia/Tokyo"=>"(UTC +9) Tokyo",
            "Australia/Brisbane"=>"(UTC +10) Cairns",
            "Pacific/Efate"=>"(UTC +11) Port Vila",
            "Asia/Anadyr"=>"(UTC +12) Anadyr"
        ];
        return $timezone;
    } 

/* end of class */
}