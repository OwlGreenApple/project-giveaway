<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Http\Request;
use App\Models\Messages;
use App\Models\Phone;
use App\Models\User;
use App\Helpers\Custom;
use App\Http\Controllers\DeviceController AS Device;
use App\Console\Commands\CheckDeviceStatus AS CDV;

class RunningMessages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'running:message';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run message and send according on table message as auto reply';

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
        $device = new Device;
        $msg = Messages::where('status',0)->orderBy('id','asc')->get();
        $arr = array(6,8,9,10,14,12);
        shuffle($arr);

        if($msg->count() > 0)
        {
            $message = ''; //define variable to add sposor message according on package
            foreach($msg as $x=>$row):
                $phone = Phone::where('user_id',$row->user_id)->first();
                $user = User::find($row->user_id);

                if(is_null($phone))
                {
                    continue;
                }

                // CHECK DEVICE STATUS AND COUNTER DAILY
                $cd = new CDV;
                $check_device = $cd->check_device($phone);

                if($phone->status == 0 || $user->counter_send_message_daily < 1)
                {
                    continue;
                }

                // DELAY INTERVAL TO AVOID BANNED FROM WA
                sleep($arr[$x]);

                $message = $row->message;

                if($row->img_url == null)
                {
                    //SEND MESSAGE
                    $data = [
                        'number'=>$row->receiver,
                        'message'=>$message,
                        'user_id'=>$row->user_id,
                        'msg_id'=>$row->id
                    ];
                    $req = new Request($data);
                    $send = $device->send_message($req);
                }
                else
                {
                    //SEND MEDIA MESSAGE
                    $data = [
                        'number'=>$row->receiver,
                        'message'=>$message,
                        "media"=> $row->img_url,
                        'user_id'=>$row->user_id,
                        'msg_id'=>$row->id
                    ];
                    $req = new Request($data);
                    $send = $device->send_media($req);
                }
            endforeach;
        }
    }

    /* end class */
}
