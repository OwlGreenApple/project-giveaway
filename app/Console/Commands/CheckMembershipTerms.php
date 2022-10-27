<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Membership;
use App\Helpers\Waweb;
use App\Models\Phone;
use Carbon\Carbon;

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
        $membership = User::where([['status','>',0],['is_admin',0],['membership','<>','free']])->whereRaw("CONVERT_TZ (NOW(), '+00:00','+07:00') >= STR_TO_DATE(end_membership,'%Y-%m-%d %H:%i:%s')")->select('id','status')->get();
        if($membership->count() > 0)
        {
           foreach($membership as $row):
             $user = User::find($row->id);
            
             /* if($row->status == 2)
             {
                $mb = Membership::where([['user_id',$row->id],['status','=',0]])->get()->count();

                if($mb < 1)
                {
                    $user->status = 1;
                }
             } */

            //  logging out connected phone
             $phones = Phone::where([['user_id',$user->id],['status','>',0]])->select('id')->get();
             self::phone_logout($phones);
           
             if($phones->count() < 1)
             {
                $user->membership = 'free';
             }
             else
             {
                $user->status = 3;
             }
             $user->save();
           endforeach;
        }
    }

    public static function phone_logout($phones)
    {
        if($phones->count() > 0)
        {
            $api = new Waweb;
            foreach($phones as $row):
                $api->logout($row->id);
            endforeach;
        }
    }

/* end console */
}
