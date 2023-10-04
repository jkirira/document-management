<?php

namespace App\Notifications;

use App\Models\DocumentAccess;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DocumentAccessExpired extends Notification
{
    use Queueable;

    protected $documentAccess;

    /**
     * Create a new notification instance.
     *
     * @param DocumentAccess $documentAccess
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
                            ->subject('Document Access Expired')
                            ->greeting('Hello!')
                            ->line('Access to the following document has expired')
                            ->line('Document: ' . ($document ? $document->name : ''))
                            ->line('Expired at: ' . Carbon::parse($this->documentAccess->expires_at)->toDayDateTimeString())
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
