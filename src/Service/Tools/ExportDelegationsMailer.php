<?php

namespace App\Service\Tools;

use App\Entity\Tools\ExportDelegationsRequest;
use Psr\Log\LoggerInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

class ExportDelegationsMailer
{
    private ExportDelegationsManager $exportDelegationsManager;

    private MailerInterface $mailer;

    private LoggerInterface $logger;

    private RouterInterface $router;

    public function __construct(ExportDelegationsManager $exportDelegationsManager, MailerInterface $mailer, LoggerInterface $logger, RouterInterface $router)
    {
        $this->exportDelegationsManager = $exportDelegationsManager;
        $this->mailer = $mailer;
        $this->logger = $logger;
        $this->router = $router;
    }

    public function sendDownloadEmail(ExportDelegationsRequest $exportDelegationsRequest): void
    {
        if (!$exportDelegationsRequest->getEmail()) {
            return;
        }
        $fullLink = $this->router->generate(
            'app_cosmos_export_delegations_show',
            ['token' => $exportDelegationsRequest->getToken()],
            UrlGeneratorInterface::ABSOLUTE_URL
        );
        $email = (new Email())
            ->to($exportDelegationsRequest->getEmail())
            ->subject('Your export is ready')
            ->text('Your export is completed and can be downloaded here: '.$exportDelegationsRequest->getDownloadLink())
            ->html('
                <p>Your export: '.$fullLink.'</p>
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

    public function sendErrorEmail(ExportDelegationsRequest $exportDelegationsRequest): void
    {
        if (!$exportDelegationsRequest->getEmail()) {
            return;
        }
        $fullLink = $this->router->generate(
            'app_cosmos_export_delegations_show',
            ['token' => $exportDelegationsRequest->getToken()],
            UrlGeneratorInterface::ABSOLUTE_URL
        );

        $email = (new Email())
            ->to($exportDelegationsRequest->getEmail())
            ->subject('Your export failed')
            ->text('Your export has failed. Please try again later.')
            ->html('
                <p>Your export: '.$fullLink.'</p>
                <p>
                    Your export has failed with the following error:
                </p>
                <p>'.$exportDelegationsRequest->getError().'</p></br>
                <p>Please make sure that the API server looks correct (if you are using custom one make sure you typed the scheme "http" or "https" and the port).</p>
                <p>If you are using public RPCs please try again with a different one. If the problem persist please reach out to us.</p><br/>
                <p>You can reach out to us on:<br/> 
                    Telegram (http://telegram.tedcrypto.io)<br/>
                    Discord (http://discord.tedcrypto.io)<br/>
                    Twitter (https://www.twitter.com/tedcrypto_)
                </p><br/>
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