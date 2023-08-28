<?php

declare(strict_types=1);

namespace App\Tests\Integration;

use Doctrine\ORM\EntityManagerInterface;
use Hautelook\AliceBundle\PhpUnit\ReloadDatabaseTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class BaseIntegrationTestCase extends KernelTestCase
{
    use ReloadDatabaseTrait;

    protected function setupFixtures(): void
    {
    }

    protected function getService(string $serviceName): object
    {
        return static::getContainer()->get($serviceName);
    }

    protected function getEntityManager(): EntityManagerInterface
    {
        return $this->getService(EntityManagerInterface::class);
    }
}
