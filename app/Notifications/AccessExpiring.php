<?php

namespace App\Notifications;

use App\Models\DocumentAccess;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AccessExpiring extends Notification
{
    use Queueable;

    public $documentAccess;

    public $sendingTo = 'user';

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(DocumentAccess $documentAccess)
    {
        $this->documentAccess = $documentAccess;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $document = $this->documentAccess->document;

        return (new MailMessage)
                    ->subject('Document Access Expiring')
                    ->greeting('Hello!')
                    ->when($this->sendingTo == 'user', function ($mailMessage) use ($document) {
                        return $mailMessage->line('Your access to ' . ($document ? $document->name : '') . ' is almost expiring.');
                    })
                    ->when($this->sendingTo == 'granter', function ($mailMessage) use ($document) {
                        return $mailMessage->line('Access to ' . ($document ? $document->name : '') . ' is almost expiring.');
                    })
                    ->line('Expires at: ' . Carbon::parse($this->documentAccess->expiry_access)->toDayDateTimeString())
                    ->line('Time Remaining: ' . Carbon::parse($this->documentAccess->expiry_access)->diffForHumans())
                    ->line('Thank you for using our application!');

    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }

    public function userNotification()
    {
        $this->sendingTo = 'user';
        return $this;
    }

    public function granterNotification()
    {
        $this->sendingTo = 'granter';
        return $this;
    }

}
