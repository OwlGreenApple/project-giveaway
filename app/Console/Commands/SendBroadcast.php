<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Events;
use App\Models\Broadcast;
use App\Models\BroadcastContestant;
use Carbon\Carbon;
use Illuminate\Database\QueryException;

class SendBroadcast extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:broadcast';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'To check and send broadcast when it need to be send';

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
        $broadcasts = Broadcast::where('status',0)->get();
        foreach($broadcasts as $broadcast) {

            $broadcastContestants = BroadcastContestant::where("broadcast_id",$broadcast->id)->get();
            foreach($broadcastContestants as $broadcastContestant){
                //send wa
            }
            $broadcast->status=1;
            $broadcast->save();
        }
    }
}
