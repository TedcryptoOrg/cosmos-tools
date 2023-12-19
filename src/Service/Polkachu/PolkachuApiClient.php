<?php

namespace App\Service\Polkachu;

use App\Model\Polkachu\CosmosUpgrades;
use GuzzleHttp\Client;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class PolkachuApiClient
{
    private readonly Client $client;

    private ?Serializer $serializer = null;

    public function __construct(private readonly CacheInterface $cache)
    {
        $this->client = new Client(
            [
                'base_uri' => 'https://polkachu.com',
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ],
                'http_errors' => false,
                'timeout' => 10,
            ]
        );
    }

    public function getCosmosUpgrades(): CosmosUpgrades
    {
        return $this->cache->get('polkachu.cosmos_upgrades', function (ItemInterface $item) {
            $item->expiresAfter(3600); // 1 hour

            $data = $this->client->get('/api/v2/chain_upgrades')->getBody()->getContents();

            return $this->getSerialiser()->deserialize($data, CosmosUpgrades::class, 'json');
        });
    }

    private function getSerialiser(): Serializer
    {
        if (null === $this->serializer) {
            $this->serializer = SerializerBuilder::create()
                // ->setPropertyNamingStrategy(new SerializedNameAnnotationStrategy(new IdenticalPropertyNamingStrategy()))
                ->build();
        }

        return $this->serializer;
    }
}
