<?php

namespace App\Message\Export;

class FetchValidatorDelegationsMessage
{
    private int $exportDelegationRequestId;

    private int $exportValidatorId;

    public function __construct(int $exportDelegationRequestId, int $exportValidatorId)
    {
        $this->exportDelegationRequestId = $exportDelegationRequestId;
        $this->exportValidatorId = $exportValidatorId;
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