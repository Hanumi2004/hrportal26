<?php

use App\Models\Setting;

if (!function_exists('setting')) {

    function setting($key, $default = [])
    {
        $value = Setting::where('key', $key)->value('value');

        if (is_null($value)) {
            return $default;
        }

        // If JSON string → decode
        if (is_string($value)) {
            $decoded = json_decode($value, true);

            return json_last_error() === JSON_ERROR_NONE
                ? $decoded
                : $default;
        }

        return $value;
    }
}
