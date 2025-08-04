<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Mail;
use App\Mail\BackupNotify;
use App\Notifications\BackupLineNotification;


Route::get('/', function () {
    return view('welcome');
});


Route::get('/run-backup', function () {
    dispatch(new \App\Jobs\DatabaseBackupJob());
    return 'Job 已派送';
});

Route::get('/run-backup/{shouldFail?}', function ($shouldFail = false) {
    dispatch(new \App\Jobs\DatabaseBackupJob($shouldFail == 'true'));
    return 'Job 已派送, shouldFail = ' . ($shouldFail == 'true' ? 'true' : 'false');
});


Route::get('/send-test-mail', function () {
    Mail::to('blakehong622@gmail.com')->send(new BackupNotify());
    return '✅ 測試信已送出';
});



Route::get('/send-line-test', function () {
    $user = User::first(); 
    $user->notify(new BackupLineNotification('📦 測試訊息：備份完成！'));
    return '✅ 已送出 LINE 通知';
});
