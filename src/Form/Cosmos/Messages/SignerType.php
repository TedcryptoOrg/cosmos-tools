<?php

namespace App\Form\Cosmos\Messages;

use App\Form\Cosmos\Messages\CreateValidator\CreateValidatorType;
use App\Service\CosmosDirectory\ChainsCosmosDirectoryClient;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;

class SignerType extends AbstractType
{
    private ChainsCosmosDirectoryClient $chainsCosmosDirectoryClient;

    public function __construct(ChainsCosmosDirectoryClient $chainsCosmosDirectoryClient)
    {
        $this->chainsCosmosDirectoryClient = $chainsCosmosDirectoryClient;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $chains = $this->chainsCosmosDirectoryClient->getAllChains();
        $networks = [];
        foreach ($chains->getChains() as $chain) {
            $networks[$chain->getPrettyName()] = $chain->getChainName();
        }
        $builder
            ->add(
                'network',
                ChoiceType::class,
                [
                    'choices' => $networks,
                ]
            )
            ->add(
                'typeUrl',
                ChoiceType::class,
                [
                    'choices' => [
                        'Create validator' => 'cosmos.staking.v1beta1.MsgCreateValidator',
                        //'Edit validator' => 'cosmos.staking.v1beta1.MsgEditValidator',
                    ],
                    'help' => 'The type of the message',
                    'required' => true,
                ]
            )
            ->add(
                'createValidatorForm',
                CreateValidatorType::class,
                [
                    'required' => false,
                    'attr' => [
                        'class' => 'type-url-forms create-validator-form',
                        'form-type-url' => 'cosmos.staking.v1beta1.MsgCreateValidator',
                    ]
                ]
            )
        ;
    }

}