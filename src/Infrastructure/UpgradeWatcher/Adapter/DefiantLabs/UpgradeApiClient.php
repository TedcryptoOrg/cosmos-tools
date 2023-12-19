<?php

namespace App\Infrastructure\UpgradeWatcher\Adapter\DefiantLabs;

use GuzzleHttp\Client;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class UpgradeApiClient
{
    private readonly Client $client;

    public function __construct(private readonly CacheInterface $cache)
    {
        $this->client = new Client(
            [
                'base_uri' => 'https://cosmos-upgrades.apis.defiantlabs.net',
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ],
                'http_errors' => false,
                'timeout' => 10,
            ]
        );
    }

    /**
     * @return array<ChainUpgradeData>
     */
    public function getCosmosUpgrades(): array
    {
        return $this->cache->get('defiantlabs.cosmos_upgrades', function (ItemInterface $item) {
            $item->expiresAfter(3600); // 1 hour

            $data = $this->client->get('/mainnets')->getBody()->getContents();
            /** @var array{type: string, network: string, latest_block_height: int, upgrade_name: string} $upgradesFound */
            $upgradesFound = array_filter(
                json_decode($data, true, 512, JSON_THROW_ON_ERROR),
                fn ($chain) => true === $chain['upgrade_found'] && null !== $chain['estimated_upgrade_time']
            );

            return \array_map(
                static function ($chain) {
                    $chainUpgradeData = new ChainUpgradeData();
                    $chainUpgradeData->type = $chain['type'];
                    $chainUpgradeData->network = $chain['network'];
                    $chainUpgradeData->rpcServer = $chain['rpc_server'];
                    $chainUpgradeData->restServer = $chain['rest_server'];
                    $chainUpgradeData->latestBlockHeight = $chain['latest_block_height'];
                    $chainUpgradeData->upgradeFound = $chain['upgrade_found'];
                    $chainUpgradeData->upgradeName = $chain['upgrade_name'];
                    $chainUpgradeData->source = $chain['source'];
                    $chainUpgradeData->upgradeBlockHeight = $chain['upgrade_block_height'];
                    $chainUpgradeData->estimatedUpgradeTime = new \DateTimeImmutable($chain['estimated_upgrade_time']);
                    $chainUpgradeData->upgradePlan = $chain['upgrade_plan'];
                    $chainUpgradeData->version = $chain['version'];
                    $chainUpgradeData->error = $chain['error'];

                    return $chainUpgradeData;
                },
                $upgradesFound
            );
        });
    }
}
