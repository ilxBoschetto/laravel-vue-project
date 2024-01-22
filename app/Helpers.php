<?php
use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

function settings($key)
{
    $settings = Cache::rememberForever('settings', function () {
        return Setting::pluck('value', 'key')->all();
    });
    if (!$settings) {
        $settings = config('settings.default');
    }


    return $settings[$key] ?? null;
}