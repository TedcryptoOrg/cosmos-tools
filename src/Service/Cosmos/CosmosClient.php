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
    private string $baseUrl;

    private Client $client;

    private string $provider;

    private Serializer $serializer;

    private LoggerInterface $logger;

    public function __construct(string $baseUrl, string $provider, Serializer $serializer, LoggerInterface $logger)
    {
        $this->baseUrl = $baseUrl;
        $this->client = new Client(
            [
                'base_uri' => rtrim($baseUrl, '/'),
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
        $headers = [
            'x-cosmos-block-height' => $height,
        ];
        $query = [
            'pagination.limit' => $limit,
            'pagination.offset' => $offset,
            'height' => $height,
        ];

        $this->logger->debug(
            'CosmosClient::getValidatorDelegations',
            [
                'url' => $this->baseUrl.$url,
                'height' => $height,
                'limit' => $limit,
                'offset' => $offset,
            ]);

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

    public function getBlockByHeight(string $height): array
    {
        $url = '/cosmos/base/tendermint/v1beta1/blocks/'.$height;
        $response = $this->client->request('GET', $url);

        if ($response->getStatusCode() !== 200) {
            $this->logger->critical($response->getBody()->getContents());
            throw new \Exception('Error while fetching height.');
        }

        $data = json_decode($response->getBody()->getContents(), true);

        return $data['block'];
    }

    public function getLatestBlockHeight(): string
    {
        $url = '/cosmos/base/tendermint/v1beta1/blocks/latest';
        $response = $this->client->request('GET', $url);

        if ($response->getStatusCode() !== 200) {
            $this->logger->critical($response->getBody()->getContents());
            throw new \Exception('Error while fetching latest height.');
        }

        $data = json_decode($response->getBody()->getContents(), true);

        return $data['block']['header']['height'];
    }

    public function getValidatorSet(?int $height): array
    {
        $url = '/cosmos/base/tendermint/v1beta1/validatorsets/'.($height ?: 'latest');
        $response = $this->client->request('GET', $url);

        if ($response->getStatusCode() !== 200) {
            $this->logger->critical($response->getBody()->getContents());
            throw new \Exception('Error while fetching validator set.');
        }

        $data = json_decode($response->getBody()->getContents(), true);

        return $data['validators'];
    }
}