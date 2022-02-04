<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Membership;

class CheckMembershipTerms extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:membership_terms';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'To check user end membership and then turned membership name if reach end';

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
        $membership = User::whereRaw("CONVERT_TZ (NOW(), '+00:00','+07:00') >= STR_TO_DATE(end_membership,'%Y-%m-%d %h:%i:%s')")->select('id','status')->get();
      
        if($membership->count() > 0)
        {
           foreach($membership as $row):
             $user = User::find($row->id);
            
             if($row->status == 2)
             {
                $mb = Membership::where([['user_id',$row->id],['status','=',0]])->get()->count();

                if($mb < 1)
                {
                    $user->status = 1;
                }
             }

             $user->membership = 'free';
             $user->save();
           endforeach;
        }
    }
}
