<?php
namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class BackupStatusNotification extends Notification
{
    use Queueable;

    public $status;
    public $message;

    public function __construct($status, $message)
    {
        $this->status  = $status;
        $this->message = $message;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        if ($this->status === 'success') {
            return (new \Illuminate\Notifications\Messages\MailMessage)
                ->subject('Database Backup Successful')
                ->line('The database backup was successful.')
                ->line('Backup file: ' . $this->message);
        } else {
            return (new \Illuminate\Notifications\Messages\MailMessage)
                ->subject('Database Backup Failed')
                ->line('The database backup failed.')
                ->line('Error: ' . $this->message);
        }
    }
}
