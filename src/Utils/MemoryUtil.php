<?php

namespace App\Utils;

class MemoryUtil
{
    public static function printMemoryUsage(): void
    {
        echo 'Memory usage: '.round(memory_get_usage() / 1024 / 1024, 2).' MB'.PHP_EOL;
    }
}