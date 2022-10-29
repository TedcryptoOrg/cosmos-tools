<?php

namespace App\Enum\Export;

class ExportStatusEnum
{
    public const PENDING = 'pending';
    public const PROCESSING = 'processing';
    public const DONE = 'done';
    public const ERROR = 'error';
    public const CANCELLED = 'cancelled';
}
