<?php

namespace App\Model\Cosmos\Authz;

use JMS\Serializer\Annotation as Serializer;

class Authorization
{
    /**
     * @Serializer\SerializedName("@type")
     */
    public string $type;

    public ?string $msg = null;

    public ?string $maxTokens = null;

    public ?string $authorizationType = null;

    /**
     * @Serializer\Type("array<App\Model\Cosmos\Authz\AllowList>")
     *
     * @var array<AllowList>
     */
    public ?array $allowList = null;

    public function getMsg(): string
    {
        if (null === $this->msg) {
            return match ($this->authorizationType) {
                'AUTHORIZATION_TYPE_DELEGATE' => '/cosmos.staking.v1beta1.MsgDelegate',
                default => $this->authorizationType,
            };
        }

        return $this->msg;
    }

    public function getAuthorizationType(): ?string
    {
        return $this->authorizationType;
    }
}
