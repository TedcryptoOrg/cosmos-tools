<?php

namespace App\Tests\Integration\Application\UseCase\RequestGrant;

use App\Application\UseCase\RequestGrant\RequestGrantCommand;
use App\Entity\Grant\FeeGrantWallet;
use App\Exception\RequestFeeGrantCommandFailed;
use App\Tests\Integration\BaseIntegrationTestCase;
use Symfony\Component\Messenger\Exception\HandlerFailedException;

class RequestGrantCommandHandlerTest extends BaseIntegrationTestCase
{
    protected function setUp(): void
    {
        self::markTestSkipped('Need to revoke fee grants and rethink the logic');
        $feeGrantWallet = new FeeGrantWallet();
        $feeGrantWallet
            ->setAddress('cosmos1')
            ->setMnemonic('mnemonic')
            ->setIsEnabled(true)
        ;
        $this->getEntityManager()->persist($feeGrantWallet);
        $this->getEntityManager()->flush();
    }

    /**
     * @doesNotPerformAssertions
     */
    public function testCommand()
    {
        try {
            $messageBus = $this->getMessageBus();
            $messageBus->dispatch(new RequestGrantCommand('cosmos1ytr0nujljr44t7kw2vhe566ecjz8mtn8n2v7xy'));
        } catch (HandlerFailedException $exception) {
            $previous = $exception->getPrevious();
            self::assertInstanceOf(RequestFeeGrantCommandFailed::class, $previous);
            self::assertStringContainsString($exception->getMessage(), 'fee allowance already exists');
        }
    }
}
