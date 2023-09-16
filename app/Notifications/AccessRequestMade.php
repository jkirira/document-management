<?php

namespace App\Notifications;

use App\Models\AccessRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AccessRequestMade extends Notification
{
    use Queueable;

    protected $accessRequest;

    /**
     * Create a new notification instance.
     *
     * @param AccessRequest $accessRequest
     */
    public function __construct(AccessRequest $accessRequest)
    {
        $this->accessRequest = $accessRequest;
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
        $document = $this->accessRequest->document;
        $requestedBy = $this->accessRequest->requestedBy;

        return (new MailMessage)
                    ->subject('Document Access Request Made')
                    ->greeting('Hello!')
                    ->line('A document access request has been made.')
                    ->line('Details:')
                    ->line('Document - ' . (isset($document) ? $document->name : ''))
                    ->line('Requesting User:')
                    ->line('Name - ' . (isset($requestedBy) ? $requestedBy->name : ''))
                    ->line('Department - ' . (isset($requestedBy->department) ? $requestedBy->department->name : ''))
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
