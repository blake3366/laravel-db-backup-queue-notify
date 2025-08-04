<?php
namespace App\Notifications\Channels;

use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class LineChannel
{
    /**
     * 發送給定通知
     */
    public function send($notifiable, Notification $notification)
    {
        if (!method_exists($notification, 'toLine')) {
            return;
        }
        
        $message = $notification->toLine($notifiable);
            // get line token 
        $token = config('services.line.token');

        $to = $notifiable->routeNotificationFor('line');
        if (empty($token) || empty($to)) {
            Log::error('LINE notification missing required parameters: token=' . ($token ? 'OK' : 'Missing') . ', to=' . ($to ? 'OK' : 'Missing'));
            return;
        }
        try {
            // 發送LINE通知
            $response = Http::withToken($token)
                ->post('https://api.line.me/v2/bot/message/push', [
                    'to' => $to,
                    'messages' => [
                        [
                            'type' => 'text',
                            'text' => $message,
                        ],
                    ],
                ]);
                
            if ($response->successful()) {
                Log::info('LINE message sent successfully');
            } else {
                Log::error('LINE message sending failed: ' . $response->body());
            }
            
            return $response;
        } catch (\Exception $e) {
            Log::error('LINE message sending failed: ' . $e->getMessage());
        }
    }
}