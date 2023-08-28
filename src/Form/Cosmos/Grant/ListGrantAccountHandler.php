<?php

declare(strict_types=1);

namespace App\Form\Cosmos\Grant;

use App\Form\AbstractFormHandler;
use App\Form\Cosmos\AccountsType;
use App\Model\Form\FormHandlerResponse;
use App\Service\Form\FormHandlerResponseInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

class ListGrantAccountHandler extends AbstractFormHandler
{

    protected function handleValidForm(Request $request, FormInterface $form, array $options): FormHandlerResponseInterface
    {

        return new FormHandlerResponse($form, true);
    }

    public function create(array $options = []): FormInterface
    {
        return $this->formFactory->create(AccountsType::class, null, $options);
    }
}
