<?php

namespace App\Service\Cosmos\Authz;

use App\Model\Cosmos\Authz\GranteeGrantsResponse;
use App\Model\Cosmos\Authz\GranterGrantsResponse;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;
use Psr\Log\LoggerInterface;

class AuthzClient
{
    private readonly Client $client;

    public function __construct(
        private readonly string $baseUrl,
        private readonly string $provider,
        private readonly Serializer $serializer,
        private readonly LoggerInterface $logger
    ) {
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
    }

    /**
     * @throws GuzzleException
     * @throws \JsonException
     */
    public function getListGrantsByGrantee(string $grantee): GranteeGrantsResponse
    {
        $url = 'cosmos/authz/v1beta1/grants/grantee/'.$grantee;
        $response = $this->client->request('GET', $url);

        if (200 !== $response->getStatusCode()) {
            $this->logger->critical($response->getBody()->getContents());
            throw new \Exception('Error while fetching grants by grantee.');
        }

        return $this->serializer->deserialize($response->getBody()->getContents(), GranteeGrantsResponse::class, 'json');
    }

    /**
     * @throws GuzzleException
     * @throws \JsonException
     */
    public function getListGrantsByGranter(string $granter): GranterGrantsResponse
    {
        $url = 'cosmos/authz/v1beta1/grants/granter/'.$granter;
        $response = $this->client->request('GET', $url);

        if (200 !== $response->getStatusCode()) {
            $this->logger->critical($response->getBody()->getContents());
            throw new \Exception('Error while fetching grants by granter.');
        }

        return $this->serializer->deserialize($response->getBody()->getContents(), GranterGrantsResponse::class, 'json');
    }
}