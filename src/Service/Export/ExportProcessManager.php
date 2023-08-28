<?php

namespace App\Service\Export;

use App\Entity\Export\ExportProcess;
use App\Entity\Export\Validator;
use App\Entity\Tools\ExportDelegationsRequest;
use App\Service\CosmosDirectory\ValidatorCosmosDirectoryClient;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;
use Psr\Log\LoggerInterface;

class ExportProcessManager
{
    public function __construct(private readonly EntityManagerInterface $entityManager, private readonly LoggerInterface $logger, private readonly ValidatorCosmosDirectoryClient $validatorCosmosDirectoryClient)
    {
    }

    public function create(ExportDelegationsRequest $request): ExportProcess
    {
        /** @var ExportProcess|null $export */
        $export = $this->getRepository()->findOneBy(['network' => $request->getNetwork(), 'height' => $request->getHeight()]);
        if (null !== $export) {
            $this->logger->info('We found one valid export already for this request');
            $export->addExportDelegationsRequest($request);
            $this->entityManager->flush();

            return $export;
        }

        return $this->entityManager->wrapInTransaction(function () use ($request) {
            $export = new ExportProcess();
            $export
                ->setNetwork($request->getNetwork())
                ->setHeight($request->getHeight())
                ->addExportDelegationsRequest($request)
            ;

            $validators = $this->validatorCosmosDirectoryClient->getChain($request->getNetwork());
            $this->logger->info('Found '.count($validators).' validators for network: '.$request->getNetwork());

            foreach ($validators as $validator) {
                $validatorEntity = new Validator();
                $validatorEntity
                    ->setValidatorAddress($validator['address'])
                    ->setValidatorName($validator['moniker'])
                ;

                $export->addValidator($validatorEntity);
            }

            $this->entityManager->persist($export);

            return $export;
        });
    }

    private function getRepository(): ObjectRepository
    {
        return $this->entityManager->getRepository(ExportProcess::class);
    }
}
