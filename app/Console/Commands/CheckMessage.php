<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Messages;
use App\Models\User;
use App\Models\Phone;
use App\Console\Commands\CheckDeviceStatus AS CDV;

class CheckMessage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:message';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check message status and then update, only for phone / device with service_id = 0';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $msg = Messages::whereIn('messages.status',[1,2])->where([['messages.phone_id','>',0],['phones.service_id',0]])
                ->join('phones','phones.id','=','messages.phone_id')
                ->select('messages.id','messages.msg_id','messages.phone_id','phones.ip_server')->get();
    
        if($msg->count() > 0)
        {
            foreach($msg as $row):
                self::get_message_status($row->msg_id,$row->id,$row->ip_server);
            endforeach;
        }
    }

    public static function get_message_status($message_id,$msg_id,$ip)
    {
        $url = $ip."/message-check/?msg_id=".$message_id;

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_VERBOSE, 0);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0);
        curl_setopt($ch, CURLOPT_TIMEOUT, 360);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
        ));

        $result = curl_exec($ch);
        if (curl_errno($ch) != 0 && empty($result))
        {
            return false;
        }

        curl_close($ch);
        $result = json_decode($result,true);

        $msg = Messages::find($msg_id);
        $msg->status = $result['status'];
        $msg->save();
    }
}
