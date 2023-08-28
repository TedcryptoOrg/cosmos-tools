<?php

declare(strict_types=1);

namespace App\Controller\Cosmos;

use App\Controller\BaseController;
use App\Form\Cosmos\SignerFormHandler;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SignerController extends BaseController
{
    #[Route(path: '/cosmos/signer', name: 'app_cosmos_signer')]
    public function __invoke(Request $request, SignerFormHandler $signerFormHandler): Response
    {
        $formResponse = $this->createAndHandleFormHandler($signerFormHandler, $request);

        return $this->render('cosmos/signer.html.twig', [
            'form' => $formResponse->getForm(),
        ]);
    }
}
