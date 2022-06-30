<?php

namespace App\Mail;

use App\Helpers\Custom;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Lang;
use Illuminate\Contracts\Queue\ShouldQueue;

class MembershipEmail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */

    public $no;
    public $name;
    public $package;
    public $price;
    public $total;

    public function __construct($no,$name,$package,$price,$total)
    {
        $this->no = $no;
        $this->name = $name;
        $this->package = $package;
        $this->price = $price;
        $this->total = $total;
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
        ->subject(Lang::get("order.order.mail"))
        ->view('emails.MembershipEmail')
        ->with([
          'no' => $this->no,
          'name' => $this->name,
          'package' => $this->package,
          'price' => $this->price,
          'total' => $this->total,
          'ct' => new Custom,
        ]);
    }
}
