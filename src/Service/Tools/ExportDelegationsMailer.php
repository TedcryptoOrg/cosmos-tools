<?php

namespace App\Service\Tools;

use App\Entity\Tools\ExportDelegationsRequest;
use Psr\Log\LoggerInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class ExportDelegationsMailer
{
    private ExportDelegationsManager $exportDelegationsManager;

    private MailerInterface $mailer;

    private LoggerInterface $logger;

    public function __construct(ExportDelegationsManager $exportDelegationsManager, MailerInterface $mailer, LoggerInterface $logger)
    {
        $this->exportDelegationsManager = $exportDelegationsManager;
        $this->mailer = $mailer;
        $this->logger = $logger;
    }

    public function sendDownloadEmail(ExportDelegationsRequest $exportDelegationsRequest): void
    {
        $email = (new Email())
            ->to($exportDelegationsRequest->getEmail())
            ->subject('Your export is ready')
            ->text('Your export is completed and can be downloaded here: '.$exportDelegationsRequest->getDownloadLink())
            ->html('
                <p>
                    Your export is completed and can be download <a href="'.$exportDelegationsRequest->getDownloadLink().'">here</a>.
                </p>
                <p>If you are having problems with the link, try access it directly here: '.$exportDelegationsRequest->getDownloadLink().'</p>
                <p>The storage where your file is located is ephemeral, which means this file will be removed at any time the machine reboots. Please download as soon as possible!</p>
                <p>Thank you!</p>    
            ');

        try {
            $this->mailer->send($email);
        } catch (\Exception $e) {
            $this->logger->error('Error sending email for request "'.$exportDelegationsRequest->getId().'": '.$e->getMessage());
            $this->exportDelegationsManager->flagAsErrored($exportDelegationsRequest, $e->getMessage());
        }
    }
}