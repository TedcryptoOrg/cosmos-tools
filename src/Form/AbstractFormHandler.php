<?php

namespace App\Form;

use App\Model\Form\FormHandlerResponse;
use App\Service\Form\FormHandlerResponseInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\OptionsResolver\OptionsResolver;

abstract class AbstractFormHandler implements FormHandlerInterface
{
    public function __construct(protected FormFactoryInterface $formFactory)
    {
    }

    public function handle(Request $request, FormInterface $form, array $options = []): FormHandlerResponseInterface
    {
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            return $this->handleValidForm($request, $form, $options);
        }

        return $this->handleInvalidForm($request, $form, $options);
    }

    public function configureOptions(OptionsResolver $optionsResolver)
    {
    }

    abstract protected function handleValidForm(Request $request, FormInterface $form, array $options): FormHandlerResponseInterface;

    protected function handleInvalidForm(Request $request, FormInterface $form, array $options): FormHandlerResponseInterface
    {
        return new FormHandlerResponse($form, false);
    }
}
