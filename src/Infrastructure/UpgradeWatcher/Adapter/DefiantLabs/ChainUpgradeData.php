<?php

namespace App\Infrastructure\UpgradeWatcher\Adapter\DefiantLabs;

class ChainUpgradeData
{
    public string $type;

    public string $network;

    public string $rpcServer;

    public string $restServer;

    public int $latestBlockHeight;

    public bool $upgradeFound;

    public string $upgradeName;

    public string $source;

    public int $upgradeBlockHeight;

    public \DateTimeImmutable $estimatedUpgradeTime;

    /**
     * @var array{height: int, binaries: array<string>, name: string, upgrade_client_state: string|null}|null
     */
    public ?array $upgradePlan = null;

    public string $version;

    public ?string $error = null;
}
