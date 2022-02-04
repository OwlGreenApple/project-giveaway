<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Membership;
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
        $membership = User::whereRaw("CONVERT_TZ (NOW(), '+00:00','+07:00') >= STR_TO_DATE(end_membership,'%Y-%m-%d %h:%i:%s')")->select('id')->get()->toArray();
        
        if(count($membership) > 0)
        {
           $query = User::whereIn('id',$membership);
           return self::check_status($query);
        }
    }

    //IF USER HAVE STATUS = 2 WHICH MEAN USER HAVE ANOTHER MEMBERSHIP
    private static function check_status($query)
    {
        $status = $query->where('status',2)->select('id')->get()->toArray();
        $end = $query->where('status',1)->select('id')->get()->toArray();
       
        // if status user = 2
        if(count($status) > 0)
        {
            foreach($status as $row):
                Membership::where([['memberships.user_id',$row['id']],['memberships.status',0]])
                ->join('users','memberships.user_id','=','users.id')->select('memberships.id','memberships.order_id')->get();
            endforeach;
        }

         // if status user = 1
         if(count($end) > 0)
        {
            User::whereIn('id',$end)->update(['membership'=>'free']);
        }
    }

/* end console */
}
