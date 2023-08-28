<?php

namespace App\Controller;

use App\Form\FormHandlerInterface;
use App\Service\Form\FormHandlerManager;
use App\Service\Form\FormHandlerResponseInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class BaseController extends AbstractController
{
    public static function getSubscribedServices(): array
    {
        return [
            FormHandlerManager::class,
        ] + parent::getSubscribedServices();
    }

    protected function createAndHandleFormHandler(FormHandlerInterface $formHandler, Request $request, array $options = []): FormHandlerResponseInterface
    {
        return $this->container->get(FormHandlerManager::class)->createAndHandle($formHandler, $request, $options);
    }
}
