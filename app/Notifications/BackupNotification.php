<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Notifications\Channels\LineChannel;
use Illuminate\Support\Facades\Log;

class BackupNotification extends Notification implements ShouldQueue // Ensure the notification is queued
{
    use Queueable;

    public $status;
    public $filename;

    public function __construct($status, $filename = null)
    {
        $this->status = $status;
        $this->filename = $filename;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return [ 'mail','line']; // Specify the channels to use
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $mail = new MailMessage;
        $mail->greeting(false)
              ->salutation(false); // remove "Regards, Laravel"
        if ($this->status === 'success') {
            Log::info('Backup successful, sending email notification.');
            return $mail->subject('Database Backup Successful')
                        ->line('Your database backup was successful.')
                        ->line('filename: ' . $this->filename);
        }
        Log::info('Backup failed, sending email notification.');
        return $mail->subject('Database Backup Failed')
                    ->error()
                    ->line('Backup failed. Please check the logs for more details.');
    }
    // LineChannel 
    public function toLine($notifiable)
    {
        $emoji      = $this->status === 'success' ? '✅' : '❌';
        $textStatus = $this->status === 'success' ? 'Success' : 'Failed';

        $message = "{$emoji} Database Backup {$textStatus}！";

        // if the backup was successful, include the filename
        if ($this->status === 'success' && $this->filename) {
            $message .= "\nBackup file: {$this->filename}";
        }

        return $message;
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
