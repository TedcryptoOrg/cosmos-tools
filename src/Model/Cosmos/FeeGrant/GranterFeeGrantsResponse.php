<?php

namespace App\Model\Cosmos\FeeGrant;

use JMS\Serializer\Annotation as Serializer;

class GranterFeeGrantsResponse
{
    /**
     * @var array<Allowances>
     *
     * @Serializer\Type("array<App\Model\Cosmos\FeeGrant\Allowances>")
     */
    private array $grants;

    /**
     * @return array<Allowances>
     */
    public function getAllowances(): array
    {
        return $this->grants;
    }

    /**
     * @param array<Allowances> $grants
     */
    public function setAllowances(array $grants): void
    {
        $this->grants = $grants;
    }
}