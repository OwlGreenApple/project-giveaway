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
use App\Http\Controllers\DeviceController AS Device;
use App\Console\Commands\CheckDeviceStatus AS CDV;
use App\Http\Controllers\BroadcastController AS BDC;
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
        $device = new Device;
        $bc = Broadcast::where('status','=',0)->get();
        $bdc = new BDC;

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
                $phone = Phone::where('user_id',$user_id)->first();

                // TO AVOID ERROR IF USER DELETE PHONE / DISCONNECTED PHONE OR DELETED USER
                if(is_null($user) || is_null($phone))
                {
                    continue;
                }

                // FILTER TIME TO SEND MESSAGE ACCORDING ON TIMEZONE
                if(Carbon::now($timezone)->lt(Carbon::parse($date_send)->toDateTimeString()))
                {
                    continue;
                }

                // CHECK DEVICE STATUS AND COUNTER DAILY
                self::check_device($phone);
                $ph = Phone::find($phone->id);

                if($ph->status == 0 || $user->counter_send_message_daily < 1)
                {
                    continue;
                }

                // PARSING CONTESTANTS
                foreach($contestants as $ctid):
                    $ct = Contestants::find($ctid);

                    // TO AVOID ERROR IF USER DELETE CONTESTANTS
                    if(is_null($ct))
                    {
                        continue;
                    }

                    $msge = new Messages;
                    $msge->user_id = $user_id;
                    $msge->bc_id = $bc_id;
                    $msge->ct_id = $ctid;
                    $msge->sender = $phone->number;
                    $msge->receiver = $ct->wa_number;
                    $msge->message = $message;
                    $msge->img_url = $url;
                    $msge->save();
                endforeach; // end parsing

                 // update broadcast status
                $brc = Broadcast::find($bc_id);
                $brc->status = 1;
                $brc->save();
            } // end broadcast loop
        endif;

        /* SEND MESSAGE LOGIC */

        $messagebulk = Messages::where('status',0)->orderBy('id','asc')->get();
        $arr = array(11,8,13,6,9,12); // 59 seconds, because computer will count start from 0
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
                $phone = Phone::where('user_id',$row->user_id)->first();
                $user = User::find($row->user_id);

                if(is_null($phone) || is_null($user))
                {
                    continue;
                }

                // CHECK DEVICE STATUS AND COUNTER DAILY
                self::check_device($phone);
                $ph = Phone::find($phone->id);

                if($ph->status == 0 || $user->counter_send_message_daily < 1)
                {
                    continue;
                }

                // DELAY INTERVAL TO AVOID BANNED FROM WA
                sleep($arr[$x]);

                // print_r($arr[$x]."\n");

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
                    $device->send_message($req);
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
                    $device->send_media($req);
                }

                // UNCOMMENT IF WANT TO TEST / DEBUG
                // $mg = Messages::find($row->id);
                // $mg->status = 1;
                // $mg->save();
            endforeach;
        }
    }

    private static function check_device($phone)
    {
        $cd = new CDV;
        $cd->check_device($phone);
    }


    /* end class */
}
