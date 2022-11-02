<?php

namespace App\Controller\Cosmos;

use App\Controller\BaseController;
use App\Entity\Tools\ExportDelegationsRequest;
use App\Form\Cosmos\ExportDelegationsFormHandler;
use App\Service\Tools\ExportDelegationsManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ExportDelegationController extends BaseController
{
    private ExportDelegationsManager $exportDelegationsManager;

    public function __construct(ExportDelegationsManager $exportDelegationsManager)
    {
        $this->exportDelegationsManager = $exportDelegationsManager;
    }

    /**
     * @Route("/cosmos/export-delegations", name="app_cosmos_export_delegations")
     */
    public function __invoke(Request $request, ExportDelegationsFormHandler $exportDelegationsFormHandler): Response
    {
        $formResponse = $this->createAndHandleFormHandler($exportDelegationsFormHandler, $request);
        if ($formResponse->isSuccessful()) {
            $this->addFlash('success', 'Delegations export request successfully created. You will receive an email to download the file when it is ready.');

            return $this->redirectToRoute('app_cosmos_export_delegations_show', ['token' => $formResponse->getParameter('exportDelegationRequest')->getToken()]);
        }

        return $this->render('cosmos/export_delegations.html.twig', [
            'form' => $formResponse->getForm()->createView(),
        ]);
    }

    /**
     * @Route("/cosmos/export-delegations/{token}", name="app_cosmos_export_delegations_show")
     */
    public function checkAction(ExportDelegationsRequest $exportDelegationsRequest): Response
    {
        return $this->render('cosmos/export_delegations_show.html.twig', [
            'exportDelegationsRequest' => $exportDelegationsRequest,
        ]);
    }

    /**
     * @Route("/cosmos/export-delegations/{token}/cancel", name="app_cosmos_export_delegations_cancel")
     */
    public function cancelAction(ExportDelegationsRequest $exportDelegationsRequest): Response
    {
        $this->exportDelegationsManager->cancel($exportDelegationsRequest);

        return $this->redirectToRoute('app_cosmos_export_delegations_show', ['token' => $exportDelegationsRequest->getToken()]);
    }

    /**
     * @Route("/cosmos/export-delegations/{token}/retry", name="app_cosmos_export_delegations_retry")
     */
    public function retryAction(ExportDelegationsRequest $exportDelegationsRequest): Response
    {
        $this->exportDelegationsManager->retry($exportDelegationsRequest);

        return $this->redirectToRoute('app_cosmos_export_delegations_show', ['token' => $exportDelegationsRequest->getToken()]);
    }
}