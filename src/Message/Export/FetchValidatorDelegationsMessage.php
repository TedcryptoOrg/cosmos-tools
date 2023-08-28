<?php

namespace App\Message\Export;

class FetchValidatorDelegationsMessage
{
    public function __construct(private readonly int $exportDelegationRequestId, private readonly int $exportValidatorId)
    {
    }

    public function getExportDelegationRequestId(): int
    {
        return $this->exportDelegationRequestId;
    }

    public function getExportValidatorId(): int
    {
        return $this->exportValidatorId;
    }
}
