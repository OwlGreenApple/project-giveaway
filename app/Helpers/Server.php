<?php
namespace App\Helpers;

class Server
{
    public static function port(){
        $data = [
          ['localhost:3200','surabaya'], //==0
          ['192.168.88.26:3200','surabaya'], //==1
        ];
    
        return $data;
      }

/* end of class */
}
