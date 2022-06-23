<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class NewMessageContact extends Mailable
{
    use Queueable, SerializesModels;

    private $message;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($message)
    {
        $this->message = $message;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->to(['info@ennervoice.com','ism.sh@hotmail.com'])
        ->subject('رسالة جديدة - تواصل معنا')
        ->markdown('emails.contact.message')
        ->with(['message' => $this->message]);
    }
}
