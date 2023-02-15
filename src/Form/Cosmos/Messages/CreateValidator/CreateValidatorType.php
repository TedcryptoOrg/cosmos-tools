<?php

declare(strict_types=1);

namespace App\Form\Cosmos\Messages\CreateValidator;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Range;

class CreateValidatorType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'description',
                DescriptionType::class
            )
            ->add(
                'commission',
                CommissionType::class
            )
            ->add(
                'minSelfDelegation',
                TextType::class,
                [
                    'help' => 'The minimum self delegation',
                    'data' => 1,
                    'constraints' => [
                        new NotBlank(),
                    ],
                ]
            )
            ->add(
                'validatorAddress',
                TextType::class,
                [
                    'help' => 'The validator address.',
                    'constraints' => [
                        new NotBlank(),
                    ],
                ]
            )
            ->add(
                'pubkey',
                TextType::class,
                [
                    'help' => 'The validator public key (cosmos.crypto.ed25519.PubKey). Just the key part.',
                    'constraints' => [
                        new NotBlank(),
                    ],
                ]
            )
            ->add(
                'value',
                TextType::class,
                [
                    'help' => 'The amount of coins to delegate',
                    'constraints' => [
                        new NotBlank(),
                    ],
                ]
            );
    }

}