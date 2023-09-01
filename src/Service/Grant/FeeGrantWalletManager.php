<?php

namespace App\Service\Grant;

use App\Entity\Grant\FeeGrantWallet;
use App\Exception\FeeGrantNotFound;
use App\Exception\FeeGrantWalletNotFound;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;
use TedcryptoOrg\CosmosAccounts\Exception\Bech32Exception;
use TedcryptoOrg\CosmosAccounts\Util\Bech32;

class FeeGrantWalletManager
{
    private const fees = [
        'osmo' => '0.05uosmo',
        'cosmos' => '0.05uatom',
    ];

    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    /**
     * @throws FeeGrantWalletNotFound
     */
    public function getMnemonic(): string
    {
        $feeGrantWallet = $this->getRepository()->findOneBy(['isEnabled' => true]);
        if (null === $feeGrantWallet) {
            throw new FeeGrantWalletNotFound();
        }

        return $feeGrantWallet->getMnemonic();
    }

    /**
     * @return ObjectRepository<FeeGrantWallet>
     */
    private function getRepository(): ObjectRepository
    {
        return $this->entityManager->getRepository(FeeGrantWallet::class);
    }

    /**
     * @throws FeeGrantNotFound
     * @throws Bech32Exception
     */
    public function getFeeFromAddress(string $address): string
    {
        $prefix = Bech32::decode($address)[0];
        if (!isset(self::fees[$prefix])) {
            throw FeeGrantNotFound::forPrefix($prefix);
        }

        return self::fees[$prefix];
    }
}
