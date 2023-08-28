<?php

namespace App\Form;

use App\Service\Form\FormHandlerResponseInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * This is responsible for creating a form then "handling" it. It is expected to
 * be stateless.
 */
interface FormHandlerInterface
{
    /**
     * Create a form to be handled.
     *
     * @param array $options The resolved options
     */
    public function create(array $options = []): FormInterface;

    /**
     * Configure the default/required options for the handler.
     */
    public function configureOptions(OptionsResolver $optionsResolver);

    /**
     * Handle a form which was created by this handler.
     *
     * @param array $options Resolved options
     */
    public function handle(Request $request, FormInterface $form, array $options = []): FormHandlerResponseInterface;
}
