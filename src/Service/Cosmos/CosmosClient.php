<?php

namespace App\Service\Cosmos;

use App\Model\Cosmos\Staking\DelegationResponses;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;

class CosmosClient
{
    private Client $client;

    private ?Serializer $serializer = null;

    private string $provider;

    public function __construct(string $baseUrl, string $provider)
    {
        $this->client = new Client(
            [
                'base_uri' => $baseUrl,
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ],
                'http_errors' => false,
                'timeout' => 60,
            ]
        );
        $this->provider = $provider;
    }

    public function getProvider(): string
    {
        return $this->provider;
    }

    public function getValidatorDelegations(string $validator, int $limit = 1000, int $offset = 0): DelegationResponses
    {
        $response = $this->client
            ->request(
                'GET',
                '/cosmos/staking/v1beta1/validators/'.$validator.'/delegations',
                [RequestOptions::QUERY => ['pagination.limit' => $limit, 'pagination.offset' => $offset]]
            );

        if ($response->getStatusCode() !== 200) {
            var_dump($response->getBody()->getContents());
            throw new \Exception('Error while fetching delegations');
        }

        return $this->getSerialiser()->deserialize(
            $response->getBody()->getContents(),
            DelegationResponses::class,
            'json'
        );
    }

    private function getSerialiser(): Serializer
    {
        if (!$this->serializer) {
            $this->serializer = SerializerBuilder::create()
                //->setPropertyNamingStrategy(new SerializedNameAnnotationStrategy(new IdenticalPropertyNamingStrategy()))
                ->build();
        }

        return $this->serializer;
    }
}