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

    public ?string $msg = null;

    public ?string $maxTokens = null;

    public ?string $authorizationType = null;

    /**
     * @Serializer\Type("array<App\Model\Cosmos\Authz\AllowList>")
     *
     * @var array<AllowList>
     */
    public ?array $allowList = null;

    /**
     * @return string
     */
    public function getMsg(): string
    {
        if ($this->msg === null) {
            switch ($this->authorizationType) {
                case 'AUTHORIZATION_TYPE_DELEGATE':
                    return '/cosmos.staking.v1beta1.MsgDelegate';
                default:
                    return $this->authorizationType;
            }
        }

        return $this->msg;
    }

    /**
     * @return string|null
     */
    public function getAuthorizationType(): ?string
    {
        return $this->authorizationType;
    }
}