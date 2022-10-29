<?php

namespace App\Model\Cosmos\Staking;

use App\Model\Cosmos\Base\Pagination;
use JMS\Serializer\Annotation as Serializer;

class DelegationResponses
{
    /**
     * @Serializer\Type("array<App\Model\Cosmos\Staking\DelegationResponse>")
     */
    private array $delegationResponses;

    private Pagination $pagination;

    /**
     * @return DelegationResponse[]
     */
    public function getDelegationResponses(): array
    {
        return $this->delegationResponses;
    }

    public function setDelegationResponses(array $delegationResponses): void
    {
        $this->delegationResponses = $delegationResponses;
    }

    public function getPagination(): Pagination
    {
        return $this->pagination;
    }

    public function setPagination(Pagination $pagination): void
    {
        $this->pagination = $pagination;
    }
}