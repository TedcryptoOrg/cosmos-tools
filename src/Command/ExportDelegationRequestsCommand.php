<?php

namespace App\Command;

use App\Enum\Export\ExportStatusEnum;
use App\Service\Tools\ExportDelegationsManager;
use App\Service\Uploader\TedcryptoTransfer;
use Symfony\Bundle\MakerBundle\ConsoleStyle;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:export-delegation-requests',
    description: 'Check export delegation request and trigger the export',
)]
class ExportDelegationRequestsCommand extends Command
{
    private ExportDelegationsManager $exportDelegationsManager;

    private TedcryptoTransfer $tedcryptoTransfer;

    public function __construct(ExportDelegationsManager $exportDelegationsManager, TedcryptoTransfer $tedcryptoTransfer)
    {
        parent::__construct();

        $this->exportDelegationsManager = $exportDelegationsManager;
        $this->tedcryptoTransfer = $tedcryptoTransfer;
    }

    protected function configure()
    {
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $style = new ConsoleStyle($input, $output);
        $requests = $this->exportDelegationsManager->findPendingRequests();

        $style->title(sprintf('Found %d pending requests', count($requests)));
        foreach ($requests as $request) {
            $style->section(sprintf('Processing request %d', $request->getId()));
            $this->exportDelegationsManager->flagProcessing($request, ExportStatusEnum::PROCESSING);

            try {
                $style->writeln('Exporting delegations');
                $exportLocation = $this->exportDelegationsManager->exportDelegations($request);
                $style->writeln(sprintf('Exported delegations to %s', $exportLocation));
                $style->writeln('Uploading delegations');
                $downloadLink = $this->tedcryptoTransfer->upload($exportLocation, sprintf('%s_delegations%s', $request->getNetwork(), $request->getHeight() ? '_'.$request->getHeight() : ''));
                $style->writeln(sprintf('Uploaded delegations to %s', $downloadLink));
                $this->exportDelegationsManager->flagAsDone($request, $downloadLink);
            } catch (\Exception $exception) {
                $this->exportDelegationsManager->flagAsErrored($request, $exception->getMessage());
                continue;
            }

            $style->writeln('Request processed');
        }

        return Command::SUCCESS;
    }
}