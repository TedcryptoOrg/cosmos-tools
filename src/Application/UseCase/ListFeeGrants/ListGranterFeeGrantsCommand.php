<?php

namespace App\Application\UseCase\ListFeeGrants;

class ListGranterFeeGrantsCommand
{
    public function __construct(
        public string $granter,
    ) {
    }
}
