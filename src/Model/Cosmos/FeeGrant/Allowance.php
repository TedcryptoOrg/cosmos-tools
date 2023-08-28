<?php

namespace App\Model\Cosmos\FeeGrant;

use JMS\Serializer\Annotation as Serializer;

class Allowance
{
    /**
     * @Serializer\SerializedName("@type")
     */
    public string $type;

    /**
     * @Serializer\Type("array<App\Model\Cosmos\FeeGrant\BasicAllowance>")
     */
    public ?array $spendLimit = null;

    public ?string $expiration = null;
}
