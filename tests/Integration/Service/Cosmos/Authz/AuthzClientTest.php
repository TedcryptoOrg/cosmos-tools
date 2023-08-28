<?php

namespace App\Tests\Integration\Service\Cosmos\Authz;

use App\Service\Cosmos\Authz\AuthzClient;
use App\Tests\Integration\BaseIntegrationTestCase;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerInterface;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class AuthzClientTest extends BaseIntegrationTestCase
{
    private AuthzClient $authzClient;

    protected function setUp(): void
    {
        $this->authzClient = new AuthzClient(
            'https://rest.cosmos.directory/osmosis/',
            'cosmos-directory',
            $this->getService(SerializerInterface::class),
            $this->getService(LoggerInterface::class)
        );
    }

    public function testGetListGrantsByGrantee(): void
    {
        $response = $this->authzClient->getListGrantsByGrantee('osmo1rd3vpw6lmvl490fkaedhrfp8ek9t7y7s5qt97t');

        self::assertCount(1, $response->getGrants());
        self::assertSame('osmo1rd3vpw6lmvl490fkaedhrfp8ek9t7y7s5qt97t', $response->getGrants()[0]->grantee);
        self::assertSame('osmo1xk23a255qm4kn6gdezr6jm7zmupn23t3mh63ya', $response->getGrants()[0]->granter);
        self::assertSame('/cosmos.authz.v1beta1.GenericAuthorization', $response->getGrants()[0]->authorization->type);
        self::assertSame('/cosmos.gov.v1beta1.MsgVote', $response->getGrants()[0]->authorization->msg);
    }

    public function testGetListGrantsByGranter(): void
    {
        $response = $this->authzClient->getListGrantsByGranter('osmo1xk23a255qm4kn6gdezr6jm7zmupn23t3mh63ya');

        self::assertCount(1, $response->getGrants());
        self::assertSame('osmo1rd3vpw6lmvl490fkaedhrfp8ek9t7y7s5qt97t', $response->getGrants()[0]->grantee);
        self::assertSame('osmo1xk23a255qm4kn6gdezr6jm7zmupn23t3mh63ya', $response->getGrants()[0]->granter);
        self::assertSame('/cosmos.authz.v1beta1.GenericAuthorization', $response->getGrants()[0]->authorization->type);
        self::assertSame('/cosmos.gov.v1beta1.MsgVote', $response->getGrants()[0]->authorization->msg);
    }
}
