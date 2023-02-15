<?php

declare(strict_types=1);

namespace App\Form\Cosmos\Messages\CreateValidator;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Range;

class CommissionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'rate',
                TextType::class,
                [
                    'help' => 'The commission rate (0-100)',
                    'data' => 5,
                    'constraints' => [
                        new NotBlank(),
                        new Range(['min' => 0, 'max' => 100]),
                    ],
                ]
            )
            ->add(
                'maxRate',
                TextType::class,
                [
                    'help' => 'The commission max rate (0-100)',
                    'data' => 5,
                    'constraints' => [
                        new NotBlank(),
                        new Range(['min' => 0, 'max' => 100]),
                    ],
                ]
            )
            ->add(
                'maxChangeRate',
                TextType::class,
                [
                    'help' => 'The commission max change rate (0-100)',
                    'data' => 5,
                    'constraints' => [
                        new NotBlank(),
                        new Range(['min' => 0, 'max' => 100]),
                    ],
                ]
            );
    }
}