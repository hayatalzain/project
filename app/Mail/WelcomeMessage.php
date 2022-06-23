<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Models\MessageNotification;

class WelcomeMessage extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    private $user;
    private $message = [];
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user)
    {
        $this->user = $user;
        $notification  = MessageNotification::where('name','welcome_signup_'.$user->lang)->first();
        $this->message['title'] = $notification->title;
        $this->message['body']  =  str_replace('#recipient', $user->username, $notification->message);

    }

    /**
     * Build the message.  
     *
     * @return $this
     */
    public function build()
    {
         return $this->to($this->user->email)
        ->subject($this->message['title'])
        ->markdown('emails.welcome-message')
        ->with(['message' => $this->message,'user'=>$this->user]);
    }
}
