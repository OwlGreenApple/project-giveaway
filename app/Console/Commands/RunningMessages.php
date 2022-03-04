<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Messages;
use App\Models\Phone;
use App\Http\Controllers\DeviceController AS Device;

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
        $msg = Messages::where('status',0)->get();
        $arr = array(6,8,9,10,14,12);
        shuffle($arr);

        if($msg->count() > 0)
        {
            foreach($msg as $x=>$row):
                sleep($arr[$x]);
                // $device->send_
            endforeach;
        }
    }

    /* end class */
}
