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
        $this->arr = [5,7,8,10,18,12];
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
        $arr = $this->arr;

         // if($msg->count() > 0)
        // {
        //     foreach($msg as $row):
        //         $device->send_
        //          sleep()
        //     endforeach;
        // }

        for($x=0;$x<=5;$x++)
        {
            if($x == 0)
            {
                echo 'sending : '."\n";
                // echo $delay[0]."\n";
                // print_r($arr);
            }
            else
            {
                $delay = $this->random_sleep($this->arr);
                echo $delay[0]."\n";
            }
        }
    }

    private function random_sleep($arr)
    {
        $index = count($arr);
        $rand = rand(0,$index);

        if(count($arr) > 0)
        {
            if(isset($arr[$rand]))
            {
                $arr = $this->cut_array($arr[$rand]);
                $this->arr = $arr;
                return $arr[$rand];
            }
            else
            {
                return $this->random_sleep($arr);
            }
        }
    }

    private function cut_array($val)
    {
        $arr = $this->arr;
        $key = array_search($val, $arr);
        unset($arr[$key]);
        return $arr;
    }
}
