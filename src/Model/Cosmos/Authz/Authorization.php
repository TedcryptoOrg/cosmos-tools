<?php

namespace App\Model\Cosmos\Authz;

use JMS\Serializer\Annotation as Serializer;

class Authorization
{
    /**
     * @var string
     * @Serializer\SerializedName("@type")
     */
    public string $type;

    public string $msg;
}