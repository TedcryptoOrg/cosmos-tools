<?php

namespace App\Tests\Integration\Service\Cosmos\Authz;

use App\Service\Cosmos\Authz\AuthzClient;
use App\Service\Cosmos\CosmosClientFactory;
use App\Tests\Integration\BaseIntegrationTestCase;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerInterface;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class AuthzClientTest extends BaseIntegrationTestCase
{
    /**
     * @dataProvider provideGetListGrantsByGrantee
     */
    public function testGetListGrantsByGrantee(string $chain, string $wallet, array $expectedGrants): void
    {
        $authzClient = $this->getAuthzClient($chain);
        $response = $authzClient->getListGrantsByGrantee($wallet);

        self::assertCount(\count($expectedGrants), $response->getGrants());
        foreach ($expectedGrants as $key => $grant) {
            self::assertSame($grant['grantee'], $response->getGrants()[$key]->grantee);
            self::assertSame($grant['granter'], $response->getGrants()[$key]->granter);
            self::assertSame($grant['authorization']['type'], $response->getGrants()[$key]->authorization->type);
            self::assertSame($grant['authorization']['msg'], $response->getGrants()[$key]->authorization->msg);
        }
    }

    public function provideGetListGrantsByGrantee(): array
    {
        return [
            [
                'chain' => 'osmosis',
                'wallet' => 'osmo1rd3vpw6lmvl490fkaedhrfp8ek9t7y7s5qt97t',
                'expectedGrants' => [
                    [
                        'grantee' => 'osmo1rd3vpw6lmvl490fkaedhrfp8ek9t7y7s5qt97t',
                        'granter' => 'osmo1xk23a255qm4kn6gdezr6jm7zmupn23t3mh63ya',
                        'authorization' => [
                            'type' => '/cosmos.authz.v1beta1.GenericAuthorization',
                            'msg' => '/cosmos.gov.v1beta1.MsgVote',
                        ],
                    ]
                ]
            ],
            [
                'chain' => 'cosmoshub',
                'wallet' => 'cosmos1ytr0nujljr44t7kw2vhe566ecjz8mtn8n2v7xy',
                'expectedGrants' => [
                    [
                        'grantee' => 'cosmos1ytr0nujljr44t7kw2vhe566ecjz8mtn8n2v7xy',
                        'granter' => 'cosmos16n2587cgz46nn5d0c5mcqlsnx8pvg566dl7gxj',
                        'authorization' => [
                            'type' => '/cosmos.authz.v1beta1.GenericAuthorization',
                            'msg' => '/cosmos.gov.v1beta1.MsgVote',
                        ],
                    ]
                ]
            ]
        ];
    }

    /**
     * @dataProvider provideGetListGrantsByGranter
     */
    public function testGetListGrantsByGranter(string $chain, string $wallet, array $expectedGrants): void
    {
        $authzClient = $this->getAuthzClient($chain);
        $response = $authzClient->getListGrantsByGranter($wallet);

        self::assertCount(\count($expectedGrants), $response->getGrants());
        foreach ($expectedGrants as $key => $grant) {
            self::assertSame($grant['grantee'], $response->getGrants()[$key]->grantee);
            self::assertSame($grant['granter'], $response->getGrants()[$key]->granter);
            self::assertSame($grant['authorization']['type'], $response->getGrants()[$key]->authorization->type);
            self::assertSame($grant['authorization']['msg'], $response->getGrants()[$key]->authorization->msg);
        }
    }

    public function provideGetListGrantsByGranter(): array
    {
        return [
            [
                'chain' => 'osmosis',
                'wallet' => 'osmo1rd3vpw6lmvl490fkaedhrfp8ek9t7y7s5qt97t',
                'expectedGrants' => []
            ],
            [
                'chain' => 'cosmoshub',
                'wallet' => 'cosmos1ytr0nujljr44t7kw2vhe566ecjz8mtn8n2v7xy',
                'expectedGrants' => [
                    [
                        'grantee' => 'cosmos1ytr0nujljr44t7kw2vhe566ecjz8mtn8n2v7xy',
                        'granter' => 'cosmos16n2587cgz46nn5d0c5mcqlsnx8pvg566dl7gxj',
                        'authorization' => [
                            'type' => '/cosmos.authz.v1beta1.GenericAuthorization',
                            'msg' => '/cosmos.gov.v1beta1.MsgVote',
                        ],
                    ]
                ]
            ]
        ];
    }

    private function getAuthzClient($chain): AuthzClient
    {
        /** @var CosmosClientFactory $clientFactory */
        $clientFactory = $this->getService(CosmosClientFactory::class);

        return $clientFactory->createAuthzClient($chain);
    }
}
