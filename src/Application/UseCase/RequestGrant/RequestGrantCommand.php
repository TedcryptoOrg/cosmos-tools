<?php

namespace App\Application\UseCase\RequestGrant;

class RequestGrantCommand
{
    public function __construct(
        public string $address,
    ) {
    }
}
