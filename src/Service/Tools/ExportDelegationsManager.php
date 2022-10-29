<?php

namespace App\Service\Tools;

use App\Entity\Tools\ExportDelegationsRequest;
use Doctrine\ORM\EntityManagerInterface;

class ExportDelegationsManager
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function createRequest(array $formData): ExportDelegationsRequest
    {
        $exportDelegationsRequest = new ExportDelegationsRequest();
        $exportDelegationsRequest
            ->setApiClient($formData['api_client'])
            ->setEmail($formData['email'])
            ->setHeight($formData['height'])
            ->setNetwork($formData['network'])
        ;

        $this->entityManager->persist($exportDelegationsRequest);
        $this->entityManager->flush();

        return $exportDelegationsRequest;
    }

}