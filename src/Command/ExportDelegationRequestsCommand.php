<?php

namespace App\Command;

use App\Enum\Export\ExportStatusEnum;
use App\Message\Tools\ExportDelegationMessage;
use App\Service\Tools\ExportDelegationsManager;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsCommand(
    name: 'app:export-delegation-requests',
    description: 'Check export delegation request and trigger the export',
)]
class ExportDelegationRequestsCommand extends Command
{
    private ExportDelegationsManager $exportDelegationsManager;

    private MessageBusInterface $bus;

    public function __construct(ExportDelegationsManager $exportDelegationsManager, MessageBusInterface $bus)
    {
        parent::__construct();

        $this->exportDelegationsManager = $exportDelegationsManager;
        $this->bus = $bus;
    }

    protected function configure()
    {
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $style = new SymfonyStyle($input, $output);
        $request = $this->exportDelegationsManager->findOnePendingRequest();
        if (!$request) {
            $style->success('No pending request found');

            return Command::SUCCESS;
        }

        $style->section(sprintf('Processing request %d', $request->getId()));
        $this->exportDelegationsManager->flagProcessing($request, ExportStatusEnum::PROCESSING);

        // Queue the old exports if any
        $this->bus->dispatch(new ExportDelegationMessage($request->getId()));

        return Command::SUCCESS;
    }
}