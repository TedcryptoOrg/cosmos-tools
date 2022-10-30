<?php

namespace App\EventSubscriber\Tools;

use App\Event\Tools\ExportDelegationsRequestDoneEvent;
use App\Service\Tools\ExportDelegationsMailer;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class MailerEventSubscriber implements EventSubscriberInterface
{
    private ExportDelegationsMailer $exportDelegationsMailer;

    public function __construct(ExportDelegationsMailer $exportDelegationsMailer)
    {
        $this->exportDelegationsMailer = $exportDelegationsMailer;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ExportDelegationsRequestDoneEvent::class => ['onExportDelegationsRequestDone', 10],
        ];
    }

    public function onExportDelegationsRequestDone(ExportDelegationsRequestDoneEvent $event)
    {
        $this->exportDelegationsMailer->sendDownloadEmail($event->getExportDelegationsRequest());
    }
}