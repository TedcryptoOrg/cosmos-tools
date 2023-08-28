<?php

namespace App\Service\Form;

use Symfony\Component\Form\FormInterface;

/**
 * Represents the form handler response.
 */
interface FormHandlerResponseInterface
{
    /**
     * Checks if a parameter exist in the bag.
     */
    public function hasParameter(string $parameter): bool;

    /**
     * Gets all parameters.
     */
    public function getAllParameters(): array;

    /**
     * Gets a specific parameter.
     */
    public function getParameter(string $parameter, mixed $default = null): mixed;

    /**
     * Returns the status of the handling of the form.
     */
    public function isSuccessful(): bool;

    /**
     * Get the form which is being handled.
     */
    public function getForm(): FormInterface;
}
