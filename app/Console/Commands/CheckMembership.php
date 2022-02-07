<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Membership;
use App\Models\Orders;
use DB;
use Carbon\Carbon;

class CheckMembership extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:membership';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'To check user\'s term of membership';

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
        $user = User::where('status','=',2)->get()->toArray();
        if(count($user) > 0)
        {
            return self::check_status($user);
        }
    }

    //IF USER HAVE STATUS = 2 WHICH MEAN USER HAVE ANOTHER MEMBERSHIP
    private static function check_status($users)
    {
        foreach($users as $row):
            $mb = Membership::where([['user_id',$row['id']],['status',0]])
                ->whereRaw("CONVERT_TZ (NOW(), '+00:00','+07:00') >= STR_TO_DATE(start,'%Y-%m-%d %h:%i:%s')")
                ->orderBy('id','asc')->first();

            if(is_null($mb))
            {
                continue;
            }

            $order = Orders::find($mb->order_id);

            if(is_null($order))
            {
                continue;
            }

            $package = $order->package;
            $user = User::find($row['id']);
            $user->membership = $package;
            $user->end_membership = $mb->end;
            $user->save();

            $mb->status = 1;
            $mb->save();
        endforeach;
       
    }

/* end console */
}
