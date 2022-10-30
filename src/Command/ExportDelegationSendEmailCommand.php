<?php

namespace App\Command;

use App\Service\Tools\ExportDelegationsMailer;
use App\Service\Tools\ExportDelegationsManager;
use Symfony\Bundle\MakerBundle\ConsoleStyle;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'tools:cosmos:export-delegations-send-email',
    description: 'Send email for delegations export',
)]
class ExportDelegationSendEmailCommand extends Command
{
    private ExportDelegationsManager $exportDelegationsManager;

    private ExportDelegationsMailer $exportDelegationsMailer;

    public function __construct(ExportDelegationsManager $exportDelegationsManager, ExportDelegationsMailer $exportDelegationMailer)
    {
        parent::__construct();

        $this->exportDelegationsManager = $exportDelegationsManager;
        $this->exportDelegationsMailer = $exportDelegationMailer;
    }

    protected function configure(): void
    {
        $this
            ->addArgument('id', InputArgument::REQUIRED, 'Export Delegation request ID')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $style = new ConsoleStyle($input, $output);
        $id = $input->getArgument('id');

        $exportDelegationRequest = $this->exportDelegationsManager->find($id);
        if (!$exportDelegationRequest) {
            $style->error('Export Delegation Request not found');

            return Command::FAILURE;
        }
        if (!$exportDelegationRequest->getEmail()) {
            $style->error('Export Delegation Request has no email');

            return Command::FAILURE;
        }

        $style->title(sprintf('Send email to "%s"', $exportDelegationRequest->getEmail()));
        $this->exportDelegationsMailer->sendDownloadEmail($exportDelegationRequest);

        $style->success('Email sent');

        return Command::SUCCESS;
    }
}