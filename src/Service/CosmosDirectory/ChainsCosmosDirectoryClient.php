<?php

namespace App\Service\CosmosDirectory;

use GuzzleHttp\Client;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class ChainsCosmosDirectoryClient
{
    private CacheInterface $cache;

    private Client $client;

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

    public function getAllChains(): array
    {
        return $this->cache->get('cosmos.directory.chains', function (ItemInterface $item) {
            $item->expiresAfter(3600); // 1 hour

            $data = $this->client->get('/')->getBody()->getContents();
            $data = json_decode($data, true);

            return $data['chains'] ?? [];
        });
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
}