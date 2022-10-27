<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Phone;
use App\Models\Messages;
use App\Helpers\Waweb;
use Carbon\Carbon;

class DeletePhoneAfterEndAweek extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'delete:phone_week';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'delete phone membership end and not renewal after 1 week';

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
        $members = User::where('status','=',3)->select('id','end_membership')->get();
    
        if($members->count() > 0)
        {
            foreach($members as $row)
            {
                //  delete phone after 1 week (7 days)
                $phones = Phone::where('user_id',$row->id)->select('id')->get();
                self::delete_phone($row->end_membership,$phones);

                // change user membership
                $user = User::find($row->id);
                $user->membership = 'free';
                $user->status = 1;
                $user->save();
            }
        }
    }

    // delete phone and erase all messages
    public static function delete_phone($end_membership,$phones)
    {
        $week = Carbon::parse($end_membership)->addDays(7);

        if(Carbon::now()->gte($week))
        {
            if($phones->count() > 0)
            {
                $api = new Waweb;
                foreach($phones as $row):
                    Messages::where('phone_id',$row->id)->delete();
                    $api->delete_device($row->id);
                endforeach;
            }
        }
    }

/* end console */
}
