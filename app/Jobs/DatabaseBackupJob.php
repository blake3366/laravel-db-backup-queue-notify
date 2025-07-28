<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Symfony\Component\Process\Process;
use Illuminate\Support\Facades\Notification;
use App\Notifications\DatabaseBackupNotification;

class DatabaseBackupJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
{
    Log::info('✅ DatabaseBackupJob starting！');
    
    $date = now()->format('Y-m-d_H-i-s');
    $filename = "backup-{$date}.sql";
    $filePath = storage_path("app/public/backups/{$filename}");

    $config = [
        'host' => 'example-host',
        'port' => '5432',
        'database' => 'example_db',
        'username' => 'example_user',
        'password' => 'example_password',
    ];

    $pgDumpPath = '/example/path/to/pg_dump';
    $command = [
        $pgDumpPath,
        '-h', $config['host'],
        '-p', $config['port'],
        '-U', $config['username'],
        $config['database']
    ];

    $env = ['PGPASSWORD' => $config['password']];

    $process = new Process($command, null, $env);
    $process->run();
    if ($process->isSuccessful()) {
        file_put_contents($filePath, $process->getOutput());
        Log::info("✅ Backup succeeded : {$filePath}");
        Notification::route('mail', 'example@example.com')
            ->notify(new DatabaseBackupNotification('success', $filename));
    } else {
        Log::error("❌ Backup Failed：" . $process->getErrorOutput());
        Notification::route('mail', 'example@example.com')
            ->notify(new DatabaseBackupNotification('fail', $process->getErrorOutput()));
    }
    Log::info('✅ DatabaseBackupJob finished！');
    }
}
