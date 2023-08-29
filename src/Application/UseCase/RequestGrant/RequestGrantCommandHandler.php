<?php

namespace App\Application\UseCase\RequestGrant;

use App\Exception\RequestFeeGrantCommandFailed;
use App\Exception\FeeGrantNotFound;
use App\Exception\FeeGrantWalletNotFound;
use App\Service\Grant\FeeGrantWalletManager;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

#[AsMessageHandler]
class RequestGrantCommandHandler
{
    public function __construct(private readonly FeeGrantWalletManager $feeGrantManager)
    {
    }

    /**
     * @throws FeeGrantWalletNotFound
     * @throws FeeGrantNotFound
     */
    public function __invoke(RequestGrantCommand $requestGrantCommand): void
    {
        $mnemonic = $this->feeGrantManager->getMnemonic();
        $fee = $this->feeGrantManager->getFeeFromAddress($requestGrantCommand->address);

        $process = Process::fromShellCommandline('ts-node scripts/grant.ts '.$requestGrantCommand->address.' "'.$mnemonic.'" '.$fee);
        try {
            $process->mustRun();
        } catch (ProcessFailedException) {
            throw new RequestFeeGrantCommandFailed($process->getErrorOutput());
        }
    }
}
