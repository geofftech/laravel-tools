<?php

use Illuminate\Support\Number;

if (!function_exists('enumIn')) {
    function enumIn($check, array $enums)
    {
        if (!$check) {
            return false;
        }

        foreach ($enums as $e) {
            if ($check === $e || $check === $e->value) {
                return true;
            }
        }

        return false;
    }
}

if (!function_exists('money')) {
    function money($value)
    {
        if ($value === null) {
            return null;
        }

        return Number::currency($value);
    }
}

if (!function_exists('summary')) {
    function summary(string $content, int $words = 10)
    {
        return Str(strip_tags($content))
            ->replace('&nbsp;', ' ')
            ->words($words);
    }
}
