<?php

namespace App\Event;

use App\Entity\Export\Validator;

class ExportValidatorCompletedEvent
{
    public function __construct(private readonly Validator $validator)
    {
    }

    public function getValidator(): Validator
    {
        return $this->validator;
    }
}
