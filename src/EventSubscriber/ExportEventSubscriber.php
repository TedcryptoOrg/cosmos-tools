<?php

namespace App\EventSubscriber;

use App\Event\ExportValidatorCompletedEvent;
use App\Service\Tools\ExportDelegationsMailer;
use App\Service\Tools\ExportDelegationsManager;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ExportEventSubscriber implements EventSubscriberInterface
{
    public function __construct(private readonly LoggerInterface $logger, private readonly ExportDelegationsManager $exportDelegationsManager, private readonly ExportDelegationsMailer $exportDelegationsMailer)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ExportValidatorCompletedEvent::class => ['onExportValidatorCompleted', 0],
        ];
    }

    public function onExportValidatorCompleted(ExportValidatorCompletedEvent $event): void
    {
        $validator = $event->getValidator();
        $export = $validator->getExportProcess();
        foreach ($export->getValidators() as $exportValidator) {
            if (!$exportValidator->isCompleted()) {
                return;
            }
        }
        $this->logger->info(sprintf('[Request: %s] All validators completed', $export->getId()));

        $export
            ->setIsCompleted(true)
            ->setCompletedAt(new \DateTime())
        ;

        foreach ($export->getExportDelegationsRequests() as $exportDelegationsRequest) {
            $this->exportDelegationsManager->flagAsDone($exportDelegationsRequest);
            $this->exportDelegationsMailer->sendDoneEmail($exportDelegationsRequest);
        }
    }
}
