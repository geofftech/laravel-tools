<?php

namespace GeoffTech\LaravelTools\Helpers;

class HtmlHelper
{

  public static function hasText(string|null $text): bool
  {
    if (!$text) {
      return false;
    }
    $stripped = strip_tags($text);
    $trimmed = trim($stripped);
    return $trimmed !== "";
  }

}