<?php

namespace App\Service\CosmosDirectory;

use App\Model\CosmosDirectory\Chains\Chains;
use GuzzleHttp\Client;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class ChainsCosmosDirectoryClient
{
    private CacheInterface $cache;

    private Client $client;

    private ?Serializer $serializer = null;

    public function __construct(CacheInterface $cache)
    {
        $this->cache = $cache;
        $this->client = new Client(
            [
                'base_uri' => 'https://chains.cosmos.directory/',
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ],
                'http_errors' => false,
                'timeout' => 10,
            ]
        );
    }

    public function getAllChains(): Chains
    {
        return $this->cache->get('cosmos.directory.chains', function (ItemInterface $item) {
            $item->expiresAfter(3600); // 1 hour

            $data = $this->client->get('/')->getBody()->getContents();

            return $this->getSerialiser()->deserialize($data, Chains::class, 'json');
        });
    }

    public function getChainKeys(): array
    {
        $chains = $this->getAllChains();
        $chainKeys = [];
        foreach ($chains->getChains() as $chain) {
            $chainKeys[$chain->getChainName()] = $chain->getName();
        }

        return $chainKeys;
    }

    public function getChain(string $chain): array
    {
        return $this->cache->get('cosmos.directory.chains.'.$chain, function (ItemInterface $item) use ($chain) {
            $item->expiresAfter(3600); // 1 hour

            $data = $this->client->get('/'.$chain)->getBody()->getContents();
            $data = json_decode($data, true);

            return $data['chain'] ?? [];
        });
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