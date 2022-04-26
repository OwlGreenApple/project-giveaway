<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Events;
use App\Models\Contestants;
use App\Models\Phone;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use App\Console\Commands\RunningMessages AS MSG;

class CheckEventStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:running_events';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'To check every running events whether have end or still running';

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
        $ev = Events::where('status',1)->get();
        $data = array();

        if($ev->count() > 0)
        {
            foreach($ev as $row):
                // print_r(Carbon::parse($row->end)->toDateTimeString());

                if(Carbon::now($row->timezone)->gte(Carbon::parse($row->end)->toDateTimeString()))
                {
                    $data[] = $row->id;
                    // get the winner and create message to send it
                    if($row->winner_run == 1)
                    {
                        self::message_for_winner($row->id);
                    }
                }
            endforeach;
        }

        if(count($data) > 0)
        {
            try
            {
                Events::whereIn('id',$data)->update(['status'=>2]);
            }
            catch(QueryException $e)
            {
                // echo $e->getMessage();
            }
        }
    }

    private static function message_for_winner($ev_id)
    {
        $ev = Events::find($ev_id);

        $ct = Contestants::where('event_id',$ev_id)->where('status',0)
            ->orderBy('entries','desc')->orderBy('date_enter', 'asc')
            ->skip(0)->take($ev->winners)->get();

        if($ct->count() > 0)
        {
            foreach($ct as $row):
                /* +++ temp +++ $phone = Phone::where([['user_id',$ev->user_id],['status',1]])->first();
                if(is_null($phone))
                {
                    $number = 0;
                    $status = 5;
                }
                else
                {
                    $number = $phone->number;
                    $status = 0;
                } +++ temp +++ */

                $msge = [
                    'user_id'=>$ev->user_id,
                    'ev_id'=>$ev->id,
                    'bc_id'=>0,
                    'ct_id'=>$row->id,
                    // 'sender'=>$number,
                    'sender'=>env('WA_TEMP'),
                    'receiver'=>substr($row->wa_number,1),
                    'message'=>$ev->winner_message,
                    // 'img_url'=>$ev->img_url,
                    'img_url'=>null,
                    // 'status'=>$status
                    'status'=>0
                ];

                $send = new MSG;
                $send::ins_message($msge);
            endforeach;
        }
    }

/* end class */
}
