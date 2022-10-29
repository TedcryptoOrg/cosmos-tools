<?php

namespace App\Form\Cosmos;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class ExportDelegationsType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $chains = [
            'Secret network' => 'secretnetwork',
        ];

        $builder
            ->add('api_client', TextType::class, [
                'label' => 'Server',
                'help' => 'The server to export the delegations from',
                'attr' => ['placeholder' => 'Leave empty to use public ones'],
                'required' => false,
            ])
            ->add('network', ChoiceType::class, [
                'label' => 'Network',
                'help' => 'The network to export the delegations from',
                'required' => true,
                'placeholder' => 'Select one network',
                'choices' => $chains,
            ])
            ->add('height', TextType::class, [
                'help' => 'Blockchain height to export the delegations from (make sure that the server has this height synced)',
                'attr' => ['placeholder' => 'Leave empty to grab latest delegations'],
                'required' => false,
            ])
            ->add('email', TextType::class, [
                'help' => 'The email to send the link to download the export when completed',
                'required' => false,
            ])
        ;
    }

}