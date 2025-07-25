<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Jobs\DatabaseBackupJob;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule::command('inspire')->everyMinute();

Artisan::command('database:backup', function () {
    $this->comment('開始備份資料庫...');
    dispatch(new DatabaseBackupJob());
    $this->comment('備份任務已派送！');
})->purpose('備份資料庫');


Schedule::command('database:backup')->everyDay()->at('10:00')
    ->onSuccess(function () {
        Log::info('✅ 資料庫備份任務成功執行！');
    })
    ->onFailure(function () {
        Log::error('⛔ 資料庫備份任務執行失敗！');
    });   