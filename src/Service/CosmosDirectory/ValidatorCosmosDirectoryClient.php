<?php

namespace App\Service\CosmosDirectory;

use GuzzleHttp\Client;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class ValidatorCosmosDirectoryClient
{
    private readonly Client $client;

    public function __construct(private readonly CacheInterface $cache)
    {
        $this->client = new Client(
            [
                'base_uri' => 'https://validators.cosmos.directory/',
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
        return $this->cache->get('cosmos.directory.validators', function (ItemInterface $item) {
            $item->expiresAfter(3600);

            $data = $this->client->get('/')->getBody()->getContents();
            $data = json_decode($data, true, 512, JSON_THROW_ON_ERROR);

            return $data['validators'] ?? [];
        });
    }

    public function getChain(string $chain): array
    {
        return $this->cache->get('cosmos.directory.validators.'.$chain, function (ItemInterface $item) use ($chain) {
            $item->expiresAfter(3600);

            $data = $this->client->get('/chains/'.$chain)->getBody()->getContents();
            $data = json_decode($data, true, 512, JSON_THROW_ON_ERROR);

            return $data['validators'] ?? [];
        });
    }

    public function getValidator(string $chain, string $valoperAddress): array
    {
        return $this->cache->get('cosmos.directory.validators.'.$valoperAddress, function (ItemInterface $item) use ($chain, $valoperAddress) {
            $item->expiresAfter(3600);

            $data = $this->client->get('/chains/'.$chain.'/'.$valoperAddress)->getBody()->getContents();
            $data = json_decode($data, true, 512, JSON_THROW_ON_ERROR);

            return $data['validator'] ?? [];
        });
    }
}
