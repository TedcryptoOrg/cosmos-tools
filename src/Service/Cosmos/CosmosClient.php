<?php

namespace App\Service\Cosmos;

use App\Model\Cosmos\Staking\DelegationResponses;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;
use Psr\Log\LoggerInterface;

class CosmosClient
{
    private Client $client;

    private string $provider;

    private Serializer $serializer;

    private LoggerInterface $logger;

    public function __construct(string $baseUrl, string $provider, Serializer $serializer, LoggerInterface $logger)
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
        $this->serializer = $serializer;
        $this->logger = $logger;
    }

    public function getProvider(): string
    {
        return $this->provider;
    }

    public function getValidatorDelegations(string $validator, ?string $height, int $limit = 1000, int $offset = 0): DelegationResponses
    {
        $url = '/cosmos/staking/v1beta1/validators/'.$validator.'/delegations';
        $headers = [];
        $query = [
            'pagination.limit' => $limit,
            'pagination.offset' => $offset,
        ];
        if ($height) {
            $query['height'] = $height;
            $headers['x-cosmos-block-height'] = $height;
        }

        $this->logger->debug('CosmosClient::getValidatorDelegations', ['url' => $url, 'height' => $height, 'limit' => $limit, 'offset' => $offset]);

        $response = $this->client->request('GET', $url, [RequestOptions::QUERY => $query, RequestOptions::HEADERS => $headers]);

        if ($response->getStatusCode() !== 200) {
            $this->logger->critical($response->getBody()->getContents());
            throw new \Exception('Error while fetching delegations.');
        }

        return $this->serializer->deserialize(
            $response->getBody()->getContents(),
            DelegationResponses::class,
            'json'
        );
    }
}