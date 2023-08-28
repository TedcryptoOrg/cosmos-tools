<?php

namespace App\Service\Cosmos\FeeGrant;

use App\Model\Cosmos\FeeGrant\GranteeFeeGrantsResponse;
use App\Model\Cosmos\FeeGrant\GranterFeeGrantsResponse;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use JMS\Serializer\Serializer;
use Psr\Log\LoggerInterface;

class FeeGrantClient
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
    public function getFeeGrantsByGrantee(string $grantee): GranteeFeeGrantsResponse
    {
        $url = 'cosmos/feegrant/v1beta1/allowances/'.$grantee;
        $response = $this->client->request('GET', $url);

        if (200 !== $response->getStatusCode()) {
            $this->logger->critical($response->getBody()->getContents());
            throw new \Exception('Error while fetching grants by grantee.');
        }

        return $this->serializer->deserialize($response->getBody()->getContents(), GranteeFeeGrantsResponse::class, 'json');
    }

    /**
     * @throws GuzzleException
     * @throws \JsonException
     */
    public function getFeeGrantsByGranter(string $granter): GranterFeeGrantsResponse
    {
        $url = 'cosmos/feegrant/v1beta1/issued/'.$granter;
        $response = $this->client->request('GET', $url);

        if (200 !== $response->getStatusCode()) {
            $this->logger->critical($response->getBody()->getContents());
            throw new \Exception('Error while fetching grants by granter.');
        }

        return $this->serializer->deserialize($response->getBody()->getContents(), GranterFeeGrantsResponse::class, 'json');
    }
}