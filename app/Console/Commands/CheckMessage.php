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
    protected $description = 'Check message status and then update';

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
        $msg = Messages::whereIn('status',[1,2])->select('id','user_id','msg_id')->get();

        if($msg->count() > 0)
        {
            foreach($msg as $row):
                $phone = Phone::where('user_id',$row->user_id)->first();

                if(is_null($phone))
                {
                    continue;
                }

                // CHECK DEVICE STATUS AND SENT MESSAGE
                $cd = new CDV;
                $check_device = $cd->check_device($phone);

                if($phone->status == 0 || $row->msg_id == 0)
                {
                    continue;
                }

                self::get_message_status($row->msg_id,$row->user_id,$row->id,$phone);
            endforeach;
        }
    }

    public static function get_message_status($message_id,$user_id,$msg_id,$phone)
    {
        $user = User::find($user_id);
        $url = $user->ip_server."/messages/".$message_id;

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
            'device-key: '.$phone->device_key
        ));

        $result = curl_exec($ch);
        if (curl_errno($ch) != 0 && empty($result))
        {
            return false;
        }

        curl_close($ch);
        $result = json_decode($result,true);

        if($result['status'] == 'DELIVERED')
        {
            $status = 2;
        }
        elseif($result['status'] == 'READ')
        {
            $status = 3;
        }
        else
        {
            //FAILED
            $status = 4;
        }

        $msg = Messages::find($msg_id);
        $msg->status = $status;
        $msg->save();
    }
}
