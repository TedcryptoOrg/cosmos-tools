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
        $service = static::getContainer()->get($serviceName);
        \assert(null !== $service);

        return $service;
    }

    protected function getEntityManager(): EntityManagerInterface
    {
        /** @var EntityManagerInterface $entityManager */
        $entityManager = $this->getService(EntityManagerInterface::class);

        return $entityManager;
    }
}
