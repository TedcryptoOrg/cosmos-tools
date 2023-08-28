<?php

namespace App\Form\Cosmos;

use App\Form\AbstractFormHandler;
use App\Model\Form\FormHandlerResponse;
use App\Service\Form\FormHandlerResponseInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

class AccountsFormHandler extends AbstractFormHandler
{
    public function __construct(FormFactoryInterface $formFactory)
    {
        parent::__construct($formFactory);
    }

    public function create(array $options = []): FormInterface
    {
        return $this->formFactory->create(
            AccountsType::class,
            null,
            $options + [
                'address_help_text' => 'Any cosmos address. It doesn\'t work with EVM addresses (e.g.: evmos, rebus, etc).',
            ]
        );
    }

    protected function handleValidForm(Request $request, FormInterface $form, array $options): FormHandlerResponseInterface
    {
        $formData = $form->getData();

        return new FormHandlerResponse($form, true);
    }
}
