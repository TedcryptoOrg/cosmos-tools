<?php

namespace App\Event;

use App\Entity\Export\Validator;

class ExportValidatorCompletedEvent
{
    private Validator $validator;

    public function __construct(Validator $validator)
    {
        $this->validator = $validator;
    }

    public function getValidator(): Validator
    {
        return $this->validator;
    }
}