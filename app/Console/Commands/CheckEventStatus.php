<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Events;
use Carbon\Carbon;
use Illuminate\Database\QueryException;

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
                if($row->unlimited == 1):
                    continue;
                endif;

                // print_r(Carbon::parse($row->end)->toDateTimeString());

                if(Carbon::now($row->timezone)->gte(Carbon::parse($row->end)->toDateTimeString()))
                {
                    $data[] = $row->id;
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
}
