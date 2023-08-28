<?php

namespace App\Model\Form;

use App\Service\Form\FormHandlerResponseInterface;
use Symfony\Component\Form\FormInterface;

class FormHandlerResponse implements FormHandlerResponseInterface
{
    public function __construct(private readonly FormInterface $form, private readonly bool $isSuccess, private array $parameters = [])
    {
    }

    public function getForm(): FormInterface
    {
        return $this->form;
    }

    public function isSuccessful(): bool
    {
        return $this->isSuccess;
    }

    public function hasParameter(string $parameter): bool
    {
        return array_key_exists($parameter, $this->parameters);
    }

    public function getAllParameters(): array
    {
        return $this->parameters;
    }

    public function getParameter(string $parameter, mixed $default = null): mixed
    {
        return $this->parameters[$parameter] ?? $default;
    }
}
