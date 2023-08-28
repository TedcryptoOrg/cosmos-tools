<?php

namespace App\Form\Cosmos;

use App\Form\AbstractFormHandler;
use App\Form\Cosmos\Messages\SignerType;
use App\Model\Form\FormHandlerResponse;
use App\Service\Form\FormHandlerResponseInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

class SignerFormHandler extends AbstractFormHandler
{
    public function __construct(FormFactoryInterface $formFactory)
    {
        parent::__construct($formFactory);
    }

    public function create(array $options = []): FormInterface
    {
        return $this->formFactory->create(SignerType::class, null, $options);
    }

    protected function handleValidForm(Request $request, FormInterface $form, array $options): FormHandlerResponseInterface
    {
        $formData = $form->getData();

        return new FormHandlerResponse($form, true);
    }
}
