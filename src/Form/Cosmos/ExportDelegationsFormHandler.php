<?php

namespace App\Form\Cosmos;

use App\Form\AbstractFormHandler;
use App\Message\Tools\ExportDelegationMessage;
use App\Model\Form\FormHandlerResponse;
use App\Service\Form\FormHandlerResponseInterface;
use App\Service\Tools\ExportDelegationsManager;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ExportDelegationsFormHandler extends AbstractFormHandler
{
    private ExportDelegationsManager $exportDelegationManager;

    private MessageBusInterface $bus;

    public function __construct(FormFactoryInterface $formFactory, ExportDelegationsManager $exportDelegationsManager, MessageBusInterface $bus)
    {
        parent::__construct($formFactory);

        $this->exportDelegationManager = $exportDelegationsManager;
        $this->bus = $bus;
    }

    public function create(array $options = []): FormInterface
    {
        return $this->formFactory->create(ExportDelegationsType::class, null, $options);
    }

    public function configureOptions(OptionsResolver $optionsResolver)
    {
    }

    protected function handleValidForm(Request $request, FormInterface $form, array $options): FormHandlerResponseInterface
    {
        $exportDelegationRequest = $this->exportDelegationManager->createRequest($form->getData());

        $this->bus->dispatch(new ExportDelegationMessage($exportDelegationRequest->getId()));

        return new FormHandlerResponse($form, true, ['exportDelegationRequest' => $exportDelegationRequest]);
    }
}