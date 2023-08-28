<?php

namespace App\Model\Cosmos\FeeGrant;

use JMS\Serializer\Annotation as Serializer;

class GranteeFeeGrantsResponse
{
    /**
     * @var Allowances[]
     *
     * @Serializer\Type("array<App\Model\Cosmos\FeeGrant\Allowances>")
     */
    private array $allowances;

    /**
     * @return array<Allowances>
     */
    public function getAllowances(): array
    {
        return $this->allowances;
    }

    /**
     * @param array<Allowances> $allowances
     */
    public function setAllowances(array $allowances): void
    {
        $this->allowances = $allowances;
    }
}
