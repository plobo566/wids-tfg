<?php

use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\Facades\Cache;
use App\Console\Commands\DetectBehaviorAnomalies;

if (Cache::get('wids_auto_run', false)) {
    
    $interval = Cache::get('wids_interval', 'daily');
    $evento = Schedule::command(DetectBehaviorAnomalies::class);

    match ($interval) {
        'hourly' => $evento->hourly(),
        'everyFourHours' => $evento->everyFourHours(),
        'everySixHours' => $evento->everySixHours(),
        'daily' => $evento->daily(),
        default => $evento->daily(),
    };
}