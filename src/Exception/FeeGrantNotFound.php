<?php

namespace App\Exception;

class FeeGrantNotFound extends \Exception
{
    public static function forPrefix(string $prefix)
    {
        return new self('Fee not found for prefix '.$prefix);
    }
}