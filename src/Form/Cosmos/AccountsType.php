<?php

namespace App\Form\Cosmos;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class AccountsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('address', TextType::class, [
                'help' => 'Any cosmos address. It doesn\'t work with EVM addresses (e.g.: evmos, rebus, etc).',
                'required' => true,
            ])
        ;
    }

}