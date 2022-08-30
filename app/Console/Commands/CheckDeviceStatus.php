<?php

namespace App\Console\Commands;

use Illuminate\Http\Request;
use Illuminate\Console\Command;
use App\Models\Phone;
use App\Http\Controllers\DeviceController AS Device;

class CheckDeviceStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:device';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'To check and update device status';

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
        return $this->main();
    }

    public function main()
    {
        $ph = Phone::where('service_id',0)->get();

        if($ph->count() > 0)
        {
            foreach($ph as $row):
                $this->check_device($row);
            endforeach;
        }
    }

    public function check_device($row)
    {
        $device = new Device;
        $data = [
            'cron'=>true,
            'phone_id'=>$row->id 
        ];

        $device->get_phone_status($data);
    }
}
