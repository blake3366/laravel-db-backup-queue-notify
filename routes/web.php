<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});


Route::get('/run-backup', function () {
    dispatch(new \App\Jobs\DatabaseBackupJob());
    return 'Job 已派送';
});