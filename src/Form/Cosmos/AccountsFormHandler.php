<?php

namespace App\Form\Cosmos;

use App\Form\AbstractFormHandler;
use App\Message\Export\FetchValidatorDelegationsMessage;
use App\Model\Form\FormHandlerResponse;
use App\Service\Cosmos\CosmosClientFactory;
use App\Service\Export\ExportProcessManager;
use App\Service\Form\FormHandlerResponseInterface;
use App\Service\Tools\ExportDelegationsManager;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Messenger\MessageBusInterface;

class AccountsFormHandler extends AbstractFormHandler
{
    public function __construct(FormFactoryInterface $formFactory)
    {
        parent::__construct($formFactory);
    }

    public function create(array $options = []): FormInterface
    {
        return $this->formFactory->create(AccountsType::class, null, $options);
    }

    protected function handleValidForm(Request $request, FormInterface $form, array $options): FormHandlerResponseInterface
    {
        $formData = $form->getData();

        return new FormHandlerResponse($form, true);
    }
}