<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Jadwal otomatis penarikan data pasien pulang rawat inap dari SIM RS setiap jam 07:00 pagi WITA
Schedule::command('inpatient:sync-simrs')
    ->dailyAt('07:00')
    ->timezone('Asia/Makassar')
    ->withoutOverlapping()
    ->appendOutputTo(storage_path('logs/inpatient-sync.log'));
