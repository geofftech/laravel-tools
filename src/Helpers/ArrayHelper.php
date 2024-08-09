<?php

namespace GeoffTech\LaravelTools\Helpers;

class ArrayHelper
{
    /**
     * To Snake Case
     * - convert each key to snake case version
     * - if the key exists in the $maps, use that instead
     * - if $maps is null, ignore
     *
     * - https://www.inanzzz.com/index.php/post/wm3x/converting-all-keys-into-snake-case-in-multidimensional-array-with-php
     */
    public static function toSnakeCase(array $array, array $maps): array
    {
        $result = [];

        foreach ($array as $key => $value) {
            if (array_key_exists($key, $maps)) {
                $newKey = $maps[$key];
                if ($newKey) {
                    $result[$maps[$key]] = $value;
                }
            } else {
                $key = strtolower(preg_replace('~(?<=\\w)([A-Z])~', '_$1', $key));
                $result[$key] = $value;
            }
        }

        return $result;
    }
}
