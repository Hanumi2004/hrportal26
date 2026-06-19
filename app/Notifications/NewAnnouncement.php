<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Announcement;

class NewAnnouncement extends Notification
{
    use Queueable;

    public $announcement;

    /**
     * Create a new notification instance.
     */
    public function __construct(Announcement $announcement)
    {
        $this->announcement = $announcement;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];    // add 'mail' later if needed
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toDatabase(object $notifiable): array
    {
        return [
            'message' => 'New announcement posted: ' . $this->announcement->title,
            'announcement_id' => $this->announcement->id,
            'title' => $this->announcement->title,
            'content' => $this->announcement->description,
        ];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->line('The introduction to the notification.')
            ->action('Notification Action', url('/'))
            ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}

// created automatically by running: php artisan make:notification NewAnnouncement

// Defines how a notification looks and what data it carries.
// Defines which channels to use:
// mail → email only (no DB needed)
// sms
// slack
// database → needs table
// broadcast → needs websocket -->
// In this case, we use mail and database channels.