<?php

namespace App\Controller\Cosmos;

use App\Controller\BaseController;
use App\Entity\Tools\ExportDelegationsRequest;
use App\Enum\Export\ExportStatusEnum;
use App\Form\Cosmos\ExportDelegationsFormHandler;
use App\Service\Tools\ExportDelegationsManager;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Annotation\Route;

class ExportDelegationController extends BaseController
{
    public function __construct(private readonly ExportDelegationsManager $exportDelegationsManager)
    {
    }

    #[Route(path: '/cosmos/export-delegations', name: 'app_cosmos_export_delegations')]
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

    #[Route(path: '/cosmos/export-delegations/{token}', name: 'app_cosmos_export_delegations_show')]
    public function checkAction(ExportDelegationsRequest $exportDelegationsRequest): Response
    {
        return $this->render('cosmos/export_delegations_show.html.twig', [
            'exportDelegationsRequest' => $exportDelegationsRequest,
        ]);
    }

    #[Route(path: '/cosmos/export-delegations/{token}/cancel', name: 'app_cosmos_export_delegations_cancel')]
    public function cancelAction(ExportDelegationsRequest $exportDelegationsRequest): Response
    {
        $this->exportDelegationsManager->cancel($exportDelegationsRequest);

        return $this->redirectToRoute('app_cosmos_export_delegations_show', ['token' => $exportDelegationsRequest->getToken()]);
    }

    #[Route(path: '/cosmos/export-delegations/{token}/retry', name: 'app_cosmos_export_delegations_retry')]
    public function retryAction(ExportDelegationsRequest $exportDelegationsRequest): Response
    {
        $this->exportDelegationsManager->retry($exportDelegationsRequest);

        return $this->redirectToRoute('app_cosmos_export_delegations_show', ['token' => $exportDelegationsRequest->getToken()]);
    }

    #[Route(path: '/cosmos/export-delegations/{token}/download', name: 'app_cosmos_export_delegations_download')]
    public function downloadAction(ExportDelegationsRequest $exportDelegationsRequest): StreamedResponse
    {
        $response = new StreamedResponse();
        $response->setCallback(function () use ($exportDelegationsRequest) {
            $exportProcess = $exportDelegationsRequest->getExportProcess();

            $csv = fopen('php://output', 'wb+');
            fputcsv($csv, ['validator_address', 'delegator_address', 'shares']);
            foreach ($exportProcess->getValidators() as $validator) {
                foreach ($validator->getDelegations() as $delegation) {
                    fputcsv($csv, [$validator->getValidatorAddress(), $delegation->getDelegatorAddress(), $delegation->getShares()]);
                }
            }
            fclose($csv);
        });

        $response->headers->set('Content-Type', 'text/csv; charset=utf-8; application/octet-stream');
        $response->headers->set(
            'Content-Disposition',
            sprintf('attachment; filename="%s_%s.csv"', $exportDelegationsRequest->getNetwork(), $exportDelegationsRequest->getHeight())
        );

        return $response;
    }

    #[Route(path: '/cosmos/export-delegations/{token}/status', name: 'app_cosmos_export_delegations_status')]
    public function getProgressAction(ExportDelegationsRequest $exportDelegationsRequest): JsonResponse
    {
        $percentage = 100;
        if (ExportStatusEnum::PROCESSING === $exportDelegationsRequest->getStatus()) {
            $validators = $exportDelegationsRequest->getExportProcess()->getValidators();
            $numValidators = \count($validators);
            $completed = 0;
            foreach ($validators as $validator) {
                if ($validator->isCompleted()) {
                    ++$completed;
                }
            }

            $percentage = (int) round($completed / $numValidators * 100);
            if (100 === $percentage) {
                $this->exportDelegationsManager->flagAsDone($exportDelegationsRequest);
                $status = ExportStatusEnum::DONE;
            }
        }

        return new JsonResponse([
            'success' => true,
            'status' => $exportDelegationsRequest->getStatus(),
            'percentage' => $percentage,
        ]);
    }
}
