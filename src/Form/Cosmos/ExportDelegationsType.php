<?php

namespace App\Form\Cosmos;

use App\Service\CosmosDirectory\ChainsCosmosDirectoryClient;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class ExportDelegationsType extends AbstractType
{
    public function __construct(private readonly ChainsCosmosDirectoryClient $chainsCosmosDirectoryClient)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $chains = $this->chainsCosmosDirectoryClient->getAllChains();
        $serversOptions = [];
        foreach ($chains->getChains() as $chain) {
            $collectionServices = $chain->getBestApis();
            foreach ($collectionServices->getRest() as $restServiceServer) {
                $name = sprintf('%s (%s)', $restServiceServer->getProvider() ?: 'Unknown', $restServiceServer->getAddress());
                $serversOptions[$chain->getName()][$name] = (string) $restServiceServer->getAddress();
            }
            $serversOptions[$chain->getName()]['Custom server'] = 'custom_'.$chain->getName();
        }

        $builder
            ->add('network', ChoiceType::class, [
                'label' => 'Network',
                'help' => 'The network to export the delegations from',
                'required' => true,
                'placeholder' => 'Select one network',
                'choices' => $this->chainsCosmosDirectoryClient->getChainKeys(),
            ])
            ->add('api_client', ChoiceType::class, [
                'label' => 'Server',
                'choices' => $serversOptions,
                'help' => 'The server to export the delegations from',
                'attr' => ['placeholder' => 'Leave empty to use public ones'],
                'required' => true,
            ])
            ->add('custom_api_server', TextType::class, [
                'label' => 'Server',
                'help' => 'Your custom server, e.g.: https://my-rest.domain.com:1317',
                'required' => false,
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
