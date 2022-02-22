<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Helpers\Custom;

class UserBuyEmail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */

    public $order;
    public $case;

    public function __construct($order,$case)
    {
        $this->order = $order;
        $this->case = $case;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
        ->from(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'))
        ->subject('['.env("MAIL_FROM_NAME").'] Konfirmasi Order')
        ->view('emails.UserBuyEmail')
        ->with([
          'order' => $this->order,
          'case' => $this->case,
          'pc' => new Custom,
        ]);
    }
}
