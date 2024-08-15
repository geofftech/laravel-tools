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
        if (is_null($value)) {
            return null;
        }

        return Number::currency($value / 100);
    }
}
