<?php

namespace App\Form\Cosmos;

use App\Form\AbstractFormHandler;
use App\Message\Tools\ExportDelegationMessage;
use App\Model\Form\FormHandlerResponse;
use App\Service\Cosmos\CosmosClientFactory;
use App\Service\Form\FormHandlerResponseInterface;
use App\Service\Tools\ExportDelegationsManager;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Messenger\MessageBusInterface;

class ExportDelegationsFormHandler extends AbstractFormHandler
{
    private ExportDelegationsManager $exportDelegationManager;

    private MessageBusInterface $bus;

    private CosmosClientFactory $cosmosClientFactory;

    public function __construct(FormFactoryInterface $formFactory, ExportDelegationsManager $exportDelegationsManager, MessageBusInterface $bus, CosmosClientFactory $cosmosClientFactory)
    {
        parent::__construct($formFactory);

        $this->exportDelegationManager = $exportDelegationsManager;
        $this->bus = $bus;
        $this->cosmosClientFactory = $cosmosClientFactory;
    }

    public function create(array $options = []): FormInterface
    {
        return $this->formFactory->create(ExportDelegationsType::class, null, $options);
    }

    protected function handleValidForm(Request $request, FormInterface $form, array $options): FormHandlerResponseInterface
    {
        $formData = $form->getData();
        $serverAddress = $formData['custom_api_server'] ?: $formData['api_client'];
        $server = $this->cosmosClientFactory->createClientManually($serverAddress);

        if (!$formData['height']) {
            try {
                $formData['height'] = $server->getLatestBlockHeight();
            } catch (\Throwable) {
                $error = 'Unable to get latest block height from the server. Please try again later.';
                $form->get('height')->addError(new FormError($error));

                return new FormHandlerResponse($form, false, ['error' => $error]);
            }
        }

        try {
            $server->getBlockByHeight($formData['height']);
        } catch (\Throwable) {
            $error = 'Unable to get block at height ' . $formData['height'] . ' from the server. Please try again later.';
            $form->get('height')->addError(new FormError($error));

            return new FormHandlerResponse($form, false, ['error' => $error]);
        }

        $exportDelegationRequest = $this->exportDelegationManager->createRequest($formData);

        $this->bus->dispatch(new ExportDelegationMessage($exportDelegationRequest->getId()));

        return new FormHandlerResponse($form, true, ['exportDelegationRequest' => $exportDelegationRequest]);
    }
}