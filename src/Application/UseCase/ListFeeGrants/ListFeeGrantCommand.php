<?php

namespace App\Application\UseCase\ListFeeGrants;

class ListFeeGrantCommand
{
    public function __construct(
        public string $granter,
    ) {
    }
}