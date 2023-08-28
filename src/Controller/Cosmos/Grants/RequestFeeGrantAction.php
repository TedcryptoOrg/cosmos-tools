<?php

namespace App\Controller\Cosmos\Grants;

use App\Application\UseCase\RequestGrant\RequestGrantCommand;
use App\Controller\BaseController;
use App\Exception\FeeGrantCommandFailed;
use App\Exception\FeeGrantNotFound;
use App\Exception\FeeGrantWalletNotFound;
use App\Form\Cosmos\AccountsType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

class RequestFeeGrantAction extends BaseController
{
    use HandleTrait;

    public function __construct(MessageBusInterface $messageBus)
    {
        $this->messageBus = $messageBus;
    }

    #[Route(path: '/cosmos/grants/request', name: 'app_cosmos_grants_request', methods: ['POST'])]
    public function __invoke(Request $request): JsonResponse
    {
        $form = $this->createForm(AccountsType::class);
        $form->handleRequest($request);

        if ($form->isValid()) {
            try {
                $this->handle(new RequestGrantCommand($form->get('address')->getData()));
            } catch (HandlerFailedException $exception) {
                $previous = $exception->getPrevious();
                if ($previous instanceof FeeGrantWalletNotFound) {
                    return new JsonResponse([
                        'success' => false,
                        'message' => 'No fee grant wallets available. Please contact us for more details.',
                    ], 404);
                }
                if ($previous instanceof FeeGrantNotFound) {
                    return new JsonResponse([
                        'success' => false,
                        'message' => 'No fee grant available for this blockchain. Please contact us to set it up',
                    ], 404);
                }
                if ($previous instanceof FeeGrantCommandFailed) {
                    return new JsonResponse([
                        'success' => false,
                        'message' => 'Grant command failed to run. Please contact us for more details.',
                    ], 500);
                }
            }
        }

        return new JsonResponse([
            'success' => true,
        ]);
    }
}