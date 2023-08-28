<?php

namespace App\Command\Cosmos;

use App\Model\Cosmos\Staking\DelegationResponses;
use App\Service\Cosmos\CosmosClientFactory;
use App\Service\CosmosDirectory\ValidatorCosmosDirectoryClient;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;

#[AsCommand(
    name: 'tools:cosmos:export-delegations',
    description: 'Export delegations from Cosmos',
)]
class ExportDelegationsCommand extends Command
{
    private readonly Filesystem $filesystem;

    public function __construct(private readonly ValidatorCosmosDirectoryClient $validatorCosmosDirectoryClient, private readonly CosmosClientFactory $cosmosClientFactory)
    {
        parent::__construct();
        $this->filesystem = new Filesystem();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('chain', InputArgument::REQUIRED, 'Chain')
            ->addOption('validator', null, InputArgument::OPTIONAL, 'Validator')
            ->addOption('height', null, InputArgument::OPTIONAL, 'Height', null)
            ->addOption('limit', null, InputArgument::OPTIONAL, 'Limit', 1000)
            ->addOption('api-client', null, InputArgument::OPTIONAL, 'API Client (e.g: https://rest.cosmos.network:1317)')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $style = new SymfonyStyle($input, $output);
        $chain = $input->getArgument('chain');
        $height = $input->getOption('height');
        $validator = $input->getOption('validator');
        $limit = $input->getOption('limit');
        $apiClient = $input->getOption('api-client');

        $this->prepareExportDirectory();

        if (null === $validator) {
            $validators = $this->validatorCosmosDirectoryClient->getChain($chain);
        } else {
            $validators = [['moniker' => 'manual', 'address' => $validator]];
        }

        if (null !== $apiClient) {
            $cosmosClient = $this->cosmosClientFactory->createClientManually($apiClient);
        } else {
            $cosmosClient = $this->cosmosClientFactory->createClient($chain);
        }

        $style->writeln('Using provider: '.$cosmosClient->getProvider());

        foreach ($validators as $exportValidator) {
            $style->title('Validator '.$exportValidator['moniker'].' ('.$exportValidator['address'].')');
            $page = 1;
            $offset = 0;
            $lastDelegator = null;
            while (true) {
                $style->writeln('Fetching delegations... Page '.$page);
                $delegations = $cosmosClient->getValidatorDelegations($exportValidator['address'], $height, $limit, $offset);
                if ([] === $delegations->getDelegationResponses()) {
                    $style->writeln('No more delegations!');
                    break;
                }
                if ($delegations->getDelegationResponses()[0]->getDelegation()->getDelegatorAddress() === $lastDelegator) {
                    $style->writeln('No more NEW delegations! Same delegator address as last page. '.$lastDelegator);
                    break;
                }
                $lastDelegator = $delegations->getDelegationResponses()[0]->getDelegation()->getDelegatorAddress();

                $style->write('Found '.\count($delegations->getDelegationResponses()).' delegations... Exporting...');
                $this->exportToCsv($delegations, $exportValidator['address'], $page);
                $style->writeln(' Done!');

                if (\count($delegations->getDelegationResponses()) < $limit) {
                    // We got to the end of our pagination - it seems
                    $output->writeln('This last page had less results than our limit -- guess we got to the end!');
                    break;
                }

                $offset += $limit;
                ++$page;
            }
        }

        $style->success('Done!');

        return Command::SUCCESS;
    }

    private function prepareExportDirectory()
    {
        if ($this->filesystem->exists('var/export')) {
            $this->filesystem->remove('var/export');
        }

        $this->filesystem->mkdir('var/export');
    }

    private function exportToCsv(DelegationResponses $delegations, string $validatorAddress, int $page)
    {
        if (!$this->filesystem->exists('var/export/'.$validatorAddress)) {
            $this->filesystem->mkdir('var/export/'.$validatorAddress);
        }

        $file = fopen(sprintf('delegations-%s.csv', $page), 'w');
        fputcsv($file, ['delegator_address', 'validator_address', 'shares', 'balance']);
        foreach ($delegations->getDelegationResponses() as $delegation) {
            fputcsv($file, [
                $delegation->getDelegation()->getDelegatorAddress(),
                $delegation->getDelegation()->getValidatorAddress(),
                $delegation->getBalance()->getAmount(),
                $delegation->getBalance()->getDenom(),
            ]);
        }
        fclose($file);

        $this->filesystem->rename(sprintf('delegations-%s.csv', $page), 'var/export/'.$validatorAddress.'/delegations-'.$page.'.csv');
    }
}
