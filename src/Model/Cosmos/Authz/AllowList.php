<?php

namespace App\Model\Cosmos\Authz;

use JMS\Serializer\Annotation as Serializer;

class AllowList
{
    /**
     * @Serializer\Type("array<string>")
     *
     * @var array<string>
     */
    public ?array $addresses = null;
}