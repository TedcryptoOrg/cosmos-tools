<?php

namespace App\Application\UseCase\ListFeeGrants;

class ListGranteeFeeGrantsCommand
{
    public function __construct(
        public string $grantee,
    ) {
    }
}
