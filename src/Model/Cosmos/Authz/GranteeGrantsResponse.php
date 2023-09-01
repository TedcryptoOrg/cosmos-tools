<?php

namespace App\Model\Cosmos\Authz;

use JMS\Serializer\Annotation as Serializer;

class GranteeGrantsResponse
{
    /**
     * @var Grant[]
     *
     * @Serializer\Type("array<App\Model\Cosmos\Authz\Grant>")
     */
    private array $grants;

    /**
     * @return array<Grant>
     */
    public function getGrants(): array
    {
        return $this->grants;
    }

    /**
     * @param array<Grant> $grants
     */
    public function setGrants(array $grants): void
    {
        $this->grants = $grants;
    }
}
