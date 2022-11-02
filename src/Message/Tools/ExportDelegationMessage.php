<?php

namespace App\Message\Tools;

class ExportDelegationMessage
{
    private int $exportDelegationsRequestId;

    public function __construct(int $exportDelegationsRequestId)
    {
        $this->exportDelegationsRequestId = $exportDelegationsRequestId;
    }

    public function getExportDelegationsRequestId(): int
    {
        return $this->exportDelegationsRequestId;
    }
}