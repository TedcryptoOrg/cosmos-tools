<?php

namespace App\Application\UseCase\RequestGrant;

use App\Exception\FeeGrantCommandFailed;
use App\Exception\FeeGrantNotFound;
use App\Exception\FeeGrantWalletNotFound;
use App\Service\Grant\FeeGrantWalletManager;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Process\Process;
use TedcryptoOrg\CosmosAccounts\Exception\Bech32Exception;
use TedcryptoOrg\CosmosAccounts\Util\Bech32;

#[AsMessageHandler]
class RequestGrantCommandHandler
{
    public function __construct(private readonly FeeGrantWalletManager $feeGrantManager)
    { }

    /**
     * @param RequestGrantCommand $requestGrantCommand
     *
     * @throws FeeGrantWalletNotFound
     * @throws FeeGrantNotFound
     */
    public function __invoke(RequestGrantCommand $requestGrantCommand): void
    {
        $mnemonic = $this->feeGrantManager->getMnemonic();
        $fee = $this->feeGrantManager->getFeeFromAddress($requestGrantCommand->address);

        $process = Process::fromShellCommandline('ts-node scripts/grant.ts '.$requestGrantCommand->address.' "'.$mnemonic.'" '.$fee);
        $process->mustRun();
        if ($process->isSuccessful() === false) {
            throw new FeeGrantCommandFailed($process->getErrorOutput());
        }
    }
}