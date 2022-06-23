<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\MessageNotification;
use App\Channels\FcmChannel;
use App\Channels\Messages\FcmMessage;


class PrivateMessageBetweenUsers extends Notification implements ShouldQueue
{
    use Queueable;

    private $message;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($message)
    {
        $this->message = $message;
        $notification  = MessageNotification::where('name',$message['name'])->first();
        $this->message['title'] = $notification->title;
        $this->message['body']  =  str_replace('#recipient', $message['recipient'], $notification->message);
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database',FcmChannel::class];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->line('The introduction to the notification.')
                    ->action('Notification Action', url('/'))
                    ->line('Thank you for using our application!');
    }


    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return FcmMessage
     */
    public function toFcm($notifiable)
    {
     $user_id_notify = $notifiable->id;
      return send_push_to_topic('notifications_'.$user_id_notify,$this->message);

    }


    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
   /* public function toDatabase($notifiable)
    {
        return $notifiable;
        return [
        'invoice_id' => $this->invoice->id,
        'amount' => $this->invoice->amount,
        ];

    }*/

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return $this->message;
        return [
            //
        ];
    }
}
