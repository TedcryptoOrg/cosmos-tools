<?php

namespace App\EventSubscriber\Tools;

use App\Event\Tools\ExportDelegationsRequestDoneEvent;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class MailerEventSubscriber implements EventSubscriberInterface
{
    private MailerInterface $mailer;

    private LoggerInterface $logger;

    public function __construct(MailerInterface $mailer, LoggerInterface $logger)
    {
        $this->mailer = $mailer;
        $this->logger = $logger;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ExportDelegationsRequestDoneEvent::class => ['onExportDelegationsRequestDone', 10],
        ];
    }

    public function onExportDelegationsRequestDone(ExportDelegationsRequestDoneEvent $event)
    {
        $email = (new Email())
            ->from('hello@example.com')
            ->to($event->getExportDelegationsRequest()->getDownloadLink())
            ->subject('Your export is ready')
            ->text('Your export is completed and can be downloaded here: '.$event->getExportDelegationsRequest()->getDownloadLink())
            ->html('
                <p>
                    Your export is completed and can be download <a href="'.$event->getExportDelegationsRequest()->getDownloadLink().'">here</a>.
                </p>
                <p>If you are having problems with the link, try access it directly here: '.$event->getExportDelegationsRequest()->getDownloadLink().'</p>');

        try {
            $this->mailer->send($email);
        } catch (\Exception $e) {
            $this->logger->error('Error sending email for request "'.$event->getExportDelegationsRequest()->getId().'": '.$e->getMessage());
        }
    }
}