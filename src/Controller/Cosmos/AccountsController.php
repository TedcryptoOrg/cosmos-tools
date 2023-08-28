<?php

namespace App\Controller\Cosmos;

use App\Controller\BaseController;
use App\Form\Cosmos\AccountsFormHandler;
use App\Service\CosmosDirectory\ChainsCosmosDirectoryClient;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use TedcryptoOrg\CosmosAccounts\Util\Bech32;

class AccountsController extends BaseController
{
    public function __construct(private readonly ChainsCosmosDirectoryClient $chainsCosmosDirectoryClient)
    {
    }

    #[Route(path: '/cosmos/accounts', name: 'app_cosmos_accounts')]
    public function __invoke(Request $request, AccountsFormHandler $accountsFormHandler): Response
    {
        $formResponse = $this->createAndHandleFormHandler($accountsFormHandler, $request);

        return $this->render('cosmos/accounts.html.twig', [
            'form' => $formResponse->getForm(),
        ]);
    }

    #[Route(path: '/cosmos/accounts/fetch', name: 'app_cosmos_accounts_fetch')]
    public function fetchAction(Request $request): JsonResponse
    {
        $address = $request->get('address');
        if (!$address) {
            return new JsonResponse([
                'error' => 'Address is required',
            ], 400);
        }

        $pubKey = Bech32::decode($address)[1];
        $accounts = [];
        foreach ($this->chainsCosmosDirectoryClient->getAllChains()->getChains() as $chain) {
            $accounts[] = [
                'chainName' => $chain->getChainName(),
                'address' => Bech32::encode($chain->getBech32Prefix(), $pubKey),
            ];
        }

        return new JsonResponse($accounts);
    }
}
