<?php

declare(strict_types=1);

namespace App\Form\Cosmos\Messages\CreateValidator;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class DescriptionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'moniker',
                TextType::class,
                [
                    'help' => 'The validator name',
                    'constraints' => [
                        new NotBlank(),
                    ],
                ]
            )
            ->add(
                'identity',
                TextType::class,
                [
                    'help' => 'The validator identity. From keybase.io. e.g.: "95CDA4711A25A991"',
                    'required' => false,
                ]
            )
            ->add(
                'website',
                TextType::class,
                [
                    'help' => 'The validator website. e.g.: https://twitter.com/tedcrypto_',
                    'required' => false,
                ]
            )
            ->add(
                'securityContact',
                TextType::class,
                [
                    'help' => 'The validator security contact. e.g.: josluis.lopes@gmail.com',
                    'required' => false,
                ]
            )
            ->add(
                'details',
                TextType::class,
                [
                    'help' => 'Short description about the validator.',
                    'constraints' => [
                        new NotBlank(),
                    ],
                ]
            )
        ;
    }
}