<?php

namespace App\Event\Tools;

use App\Entity\Tools\ExportDelegationsRequest;

class ExportDelegationsRequestDoneEvent
{
    private ExportDelegationsRequest $exportDelegationsRequest;

    public function __construct(ExportDelegationsRequest $exportDelegationsRequest)
    {
        $this->exportDelegationsRequest = $exportDelegationsRequest;
    }

    public function getExportDelegationsRequest(): ExportDelegationsRequest
    {
        return $this->exportDelegationsRequest;
    }
}