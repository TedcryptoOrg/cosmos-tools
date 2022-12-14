<?php

namespace App\Service\Form;

use App\Form\FormHandlerInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FormHandlerManager
{
    /**
     * Handles the form
     */
    public function createAndHandle(FormHandlerInterface $formHandler, Request $request, array $options = []): FormHandlerResponseInterface
    {
        $options = $this->resolveOptions($formHandler, $options);

        $form = $formHandler->create($options);

        return $formHandler->handle($request, $form, $options);
    }

    /**
     * Resolve/validate handler options
     *
     * @param FormHandlerInterface $formHandler
     * @param array $options
     *
     * @return array
     */
    private function resolveOptions(FormHandlerInterface $formHandler, array $options)
    {
        $optionsResolver = new OptionsResolver();
        $formHandler->configureOptions($optionsResolver);
        $options = $optionsResolver->resolve($options);

        return $options;
    }
}