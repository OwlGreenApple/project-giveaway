<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ContactEmail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $user_email;
    public $message;

    public function __construct($user_email,$message)
    {
        $this->email = $user_email;
        $this->message = $message;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
        ->from($this->email, 'user')
        ->subject('Kontak user '.env('APP_NAME').'')
        ->view('emails.contacts')
        ->with([
          'messages' => $this->message,
        ]);
    }
}
