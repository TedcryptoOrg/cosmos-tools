<?php

declare(strict_types=1);

namespace App\Controller\Cosmos\Grants;

use App\Controller\BaseController;
use App\Form\Cosmos\Grant\ListGrantAccountHandler;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ListGrantsAction extends BaseController
{
    #[Route(path: '/cosmos/grants', name: 'app_cosmos_grants_index')]
    public function __invoke(Request $request, ListGrantAccountHandler $grantAccountHandler): Response
    {
        $formResponse = $this->createAndHandleFormHandler($grantAccountHandler, $request);

        return $this->render('cosmos/grants/index.html.twig', [
            'form' => $formResponse->getForm()->createView(),
        ]);
    }
}
