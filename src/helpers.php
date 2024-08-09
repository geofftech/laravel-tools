<?php

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
