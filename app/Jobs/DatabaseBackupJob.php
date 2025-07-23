<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

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

        $config = config('database.connections.pgsql');
        $host = $config['host'];
        $port = $config['port'];
        $database = $config['database'];
        $username = $config['username'];
        $password = $config['password'];

        // if pg_dump is not in PATH, specify your pg_dump path
        // if pg_dump is in PATH, you can remove the path, change the command to just `pg_dump`
        $pgDumpPath = '/opt/homebrew/opt/postgresql@16/bin/pg_dump';
        $command = "PGPASSWORD=\"{$password}\" {$pgDumpPath} -h {$host} -p {$port} -U {$username} {$database} > {$filePath}";
        try {
            exec($command, $output, $returnCode);

            if ($returnCode !== 0) {
                throw new \Exception("備份失敗，Return code: $returnCode");
            }

            Log::info("✅ 備份成功：{$filePath}");

        } catch (\Exception $e) {
            Log::error("❌ 備份失敗：" . $e->getMessage());
        }
        Log::info('✅ DatabaseBackupJob finished！');
    }
}
