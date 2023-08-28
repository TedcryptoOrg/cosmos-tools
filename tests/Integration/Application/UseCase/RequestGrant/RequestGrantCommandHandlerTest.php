<?php

namespace App\Tests\Integration\Application\UseCase\RequestGrant;

use App\Application\UseCase\RequestGrant\RequestGrantCommand;
use App\Entity\Grant\FeeGrantWallet;
use App\Tests\Integration\BaseIntegrationTestCase;

class RequestGrantCommandHandlerTest extends BaseIntegrationTestCase
{
    protected function setUp(): void
    {
        $feeGrantWallet = new FeeGrantWallet();
        $feeGrantWallet
            ->setAddress('cosmos1')
            ->setMnemonic('picture basic all cross paper tag math innocent helmet say risk scrub sauce private tail spirit beauty track bunker satoshi swap farm mouse receive')
            ->setIsEnabled(true)
        ;
        $this->getEntityManager()->persist($feeGrantWallet);
        $this->getEntityManager()->flush();
    }

    public function testCommand()
    {
        $messageBus = $this->getMessageBus();
        $messageBus->dispatch(new RequestGrantCommand('cosmos1ytr0nujljr44t7kw2vhe566ecjz8mtn8n2v7xy'));
    }
}
