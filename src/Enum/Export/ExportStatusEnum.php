<?php

namespace App\Enum\Export;

class ExportStatusEnum
{
    final public const PENDING = 'pending';
    final public const PROCESSING = 'processing';
    final public const DONE = 'done';
    final public const ERROR = 'error';
    final public const CANCELLED = 'cancelled';
}
