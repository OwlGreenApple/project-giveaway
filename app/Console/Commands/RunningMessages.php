<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Http\Request;
use App\Models\Messages;
use App\Models\Phone;
use App\Models\User;
use App\Models\Broadcast;
use App\Models\Contestants;
use App\Helpers\Custom;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\WABlasController AS Device;
use App\Http\Controllers\BroadcastController AS BDC;
use App\Console\Commands\CheckDeviceStatus AS CDV;
use Carbon\Carbon;

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
        $device = new Messages;
        $bc = Broadcast::where('status','=',0)->get();
        $bdc = new BDC;

        // CHECK PHONE CONNECT STATUS
        self::check_phone_connect();

        if($bc->count() > 0):
            foreach($bc as $row)
            {
                $bc_id = $row->id;
                $user_id = $row->user_id;
                $date_send = $row->date_send;
                $message = $row->message;
                $url = $row->url;
                $timezone = $row->timezone;
                $contestants = $bdc::parsing_array($row->ct_list);

                $user = User::find($user_id);

                // FILTER TIME TO SEND MESSAGE ACCORDING ON TIMEZONE
                if(Carbon::now($timezone)->lt(Carbon::parse($date_send)->toDateTimeString()))
                {
                    continue;
                }

                // CHECK DEVICE STATUS AND COUNTER DAILY
                /* +++ temp +++ */
                // if($user->counter_send_message_daily < 1)
                // {
                //     continue;
                // }

                // PARSING CONTESTANTS
                foreach($contestants as $ctid):
                    $ct = Contestants::find($ctid);

                    // TO AVOID ERROR IF USER DELETE CONTESTANTS
                    if(is_null($ct))
                    {
                        continue;
                    }

                    $mg = new Messages;
                    $sender = $mg::sender($user_id);

                    $msge = [
                        'user_id'=>$user_id,
                        'ev_id'=>0,
                        'bc_id'=>$bc_id,
                        'ct_id'=>$ctid,
                        'sender'=>$sender,
                        'receiver'=>substr($ct->wa_number,1),
                        'message'=>$message,
                        'img_url'=>$url
                    ];

                    self::ins_message($msge);
                endforeach; // end parsing

                 // update broadcast status
                $brc = Broadcast::find($bc_id);
                $brc->status = 1;
                $brc->save();
            } // end broadcast loop
        endif;

        /*
            SEND MESSAGE LOGIC
        */
        $messagebulk = Messages::where('status',0)->orderBy('id','asc')->get();
        $arr = array(10,8,13,7,9,12); // 59 seconds, because computer will count start from 0
        shuffle($arr);

        $msg = $bc_id = $ev_id = array();
        if($messagebulk->count() > 0)
        {
            foreach($messagebulk as $x=>$cols):
                if($cols->bc_id > 0)
                {
                    $bc_id[] = $cols;
                }

                if($cols->ev_id > 0)
                {
                    $ev_id[] = $cols;
                }
            endforeach;
        }

        $total_bc = count($bc_id);
        $total_ev = count($ev_id);
        $total_msg = 0;

        // filter priority
        if(($total_bc > 6 && $total_ev > 6) || ($total_bc >= 4 && $total_ev >= 4))
        {
            $bc_id = array_slice($bc_id,0,4);
            $ev_id = array_slice($ev_id,0,2);
        }
        elseif($total_bc > 6 && $total_ev < 1)
        {
            $bc_id = array_slice($bc_id,0,6);
        }
        elseif($total_bc < 1 && $total_ev > 6)
        {
            $ev_id = array_slice($ev_id,0,6);
        }
        elseif($total_bc > 6 && $total_ev < 4)
        {
            $bc_id = array_slice($bc_id,0,4);
        }
        else
        {
            $ev_id = array_slice($ev_id,0,6);
        }

        $total_msg = $total_bc + $total_ev;
        $msg = array_slice(array_merge($bc_id,$ev_id),0,6);

        // LOGIC TO SENDING MESSAGE
        if(count($msg) > 0)
        {
            $message = ''; //define variable to add sposor message according on package
            foreach($msg as $x=>$row):
                $user = User::find($row->user_id);

                if(is_null($user))
                {
                    continue;
                }

                // CHECK DEVICE STATUS AND COUNTER DAILY
                /* +++ temp +++ */
                /* if($user->counter_send_message_daily < 1)
                {
                    continue;
                } */

                // DELAY INTERVAL TO AVOID BANNED FROM WA
                sleep($arr[$x]);

                // print_r($arr[$x]."\n");

                $message = $row->message;
                if($row->img_url == null)
                {
                    $image = null;
                }
                else
                {
                    $image = Storage::disk('s3')->url($row->img_url);
                }
                
                $status = $device::sendingwa($user,$row->receiver,$message,$image,$row->sender);

                // RESULT FROM SEND MESSAGE
                if(isset($status['id']))
                {
                    $msg_id = $status['id'];
                    $device_id = $status['device_id'];
                    $msg_stat = 1;
                }
                else
                {
                    $msg_id = 0;
                    $device_id = $status['device_id'];
                    $msg_stat = $status['status'];
                }

                // UPDATE MESSAGE STATUS AND ID
                $mg = Messages::find($row->id);
                $mg->phone_id = $device_id;
                $mg->msg_id = $msg_id;
                $mg->status = $msg_stat;
                $mg->save();
            endforeach;
        }
    }

    // IN CASE IF USING WAWEB API
    public static function check_phone_connect()
    {
        // CHECK WHETHER DEVICE IS CONNECTED AND RELOAD ALL DEVICE WHERE service_id = 0
        $check_phone = new CDV;
        $check_phone->main();
    }

    public static function ins_message($data)
    {
        $msge = new Messages;
        $msge->user_id = $data['user_id'];
        $msge->ev_id = $data['ev_id'];
        $msge->bc_id = $data['bc_id'];
        $msge->ct_id = $data['ct_id'];
        $msge->sender = $data['sender'];
        $msge->receiver = $data['receiver'];
        $msge->message = $data['message'];
        $msge->img_url = $data['img_url'];

        if(isset($data['status']))
        {
            $msge->status = $data['status'];
        }

        $msge->save();
    }


    /* end class */
}
