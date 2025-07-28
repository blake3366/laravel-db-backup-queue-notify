<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DatabaseBackupNotification extends Notification implements ShouldQueue // Ensure the notification is queued
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
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        if ($this->status === 'success') {
            return (new MailMessage)
            ->subject('資料庫備份成功')
            ->line('您的資料庫備份已完成。')
            ->line('檔案名稱：' . $this->filename);
        }

        return (new MailMessage)
        ->subject('資料庫備份失敗')
        ->error()
        ->line('備份失敗，請檢查伺服器狀況。');
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
