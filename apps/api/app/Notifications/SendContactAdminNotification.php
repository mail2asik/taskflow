<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SendContactAdminNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public string $name;
    public string $email;
    public string $message;

    /**
     * Create a new notification instance.
     */
    public function __construct(string $name, string $email, string $message)
    {
        $this->name = $name;
        $this->email = $email;
        $this->message = $message;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('New Contact Form Submission - TaskFlow')
            ->greeting('Hello Admin!')
            ->line('A new contact form submission has been received.')
            ->line('Name: ' . $this->name)
            ->line('Email: ' . $this->email)
            ->line('Message: ' . $this->message);
    }
}