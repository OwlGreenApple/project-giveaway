<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Events;
use App\Models\Broadcast;
use App\Models\BroadcastContestant;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use App\Helpers\Custom;

class ResetCounterSendMessage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reset:counter_send_message';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'To reset field counter_send_message_daily on users table, depends on users package';

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
        $users = User::all();
        $ct = new Custom;

        if($users->count() > 0):
            foreach($users as $user)
            {
                $user->date_counter = Carbon::now()->toDateString();
                $user->counter_send_message_daily = $ct->check_type($user->membership)['wa'];
                $user->save();
            }
        endif;
    }
}
