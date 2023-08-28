<?php

declare(strict_types=1);

namespace App\Controller\Cosmos\Grants;

use App\Application\UseCase\ListFeeGrants\ListGranteeFeeGrantsCommand;
use App\Application\UseCase\ListFeeGrants\ListGranterFeeGrantsCommand;
use App\Application\UseCase\ListGrants\ListGrantCommand;
use App\Controller\BaseController;
use App\Form\Cosmos\AccountsType;
use App\Model\Cosmos\Authz\GranterGrantsResponse;
use App\Model\Cosmos\FeeGrant\GranteeFeeGrantsResponse;
use App\Model\Cosmos\FeeGrant\GranterFeeGrantsResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

class ListGrantsAction extends BaseController
{
    use HandleTrait;

    public function __construct(
        MessageBusInterface $messageBus
    ) {
        $this->messageBus = $messageBus;
    }

    #[Route(path: '/cosmos/grants', name: 'app_cosmos_grants_index')]
    public function __invoke(Request $request): Response
    {
        $form = $this->createForm(AccountsType::class);
        $form->handleRequest($request);

        /** @var GranterGrantsResponse|null $listGrants */
        $listGrants = null;
        /** @var GranterFeeGrantsResponse|null $feeGrants */
        $feeGrants = null;
        /** @var GranteeFeeGrantsResponse|null $feeGrantee */
        $feeGrantee = null;
        if ($form->isValid()) {
            $listGrants = $this->handle(new ListGrantCommand($form->get('address')->getData()));
            $feeGrants = $this->handle(new ListGranterFeeGrantsCommand($form->get('address')->getData()));
            $feeGrantee = $this->handle(new ListGranteeFeeGrantsCommand($form->get('address')->getData()));
        }

        return $this->render('cosmos/grants/index.html.twig', [
            'form' => $form->createView(),
            'listGrants' => $listGrants,
            'feeGrants' => $feeGrants,
            'feesGrantee' => $feeGrantee,
        ]);
    }
}
