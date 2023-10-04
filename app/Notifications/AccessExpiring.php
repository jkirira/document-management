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
                    ->line('Access to ' . ($document ? $document->name : '') . ' is almost expiring.')
                    ->line('Expiry time: ' . Carbon::parse($this->documentAccess->expires_at)->toDayDateTimeString())
                    ->line('Time Remaining: ' . Carbon::parse($this->documentAccess->expires_at)->diffForHumans())
                    ->line('')
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

}
