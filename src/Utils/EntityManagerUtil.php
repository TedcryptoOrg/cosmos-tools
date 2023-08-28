<?php

namespace App\Utils;

use Doctrine\DBAL\Exception as DBALException;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

class EntityManagerUtil
{
    public function __construct(private readonly ManagerRegistry $managerRegistry, private EntityManagerInterface $entityManager)
    {
    }

    public function pingConnection(): void
    {
        $entityManager = $this->entityManager;
        $connection = $entityManager->getConnection();

        try {
            $connection->executeQuery($connection->getDatabasePlatform()->getDummySelectSQL());
        } catch (DBALException) {
            $connection->close();
            $connection->connect();
        }

        if (!$entityManager->isOpen()) {
            $this->managerRegistry->resetManager(null);
        }
    }
}
