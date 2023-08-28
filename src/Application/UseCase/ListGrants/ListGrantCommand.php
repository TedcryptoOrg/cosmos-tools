<?php

namespace App\Application\UseCase\ListGrants;

class ListGrantCommand
{
    public function __construct(
        public string $granter,
    ) {
    }
}