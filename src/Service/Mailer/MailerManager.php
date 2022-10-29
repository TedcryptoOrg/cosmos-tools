<?php

namespace App\Service\Mailer;

use Symfony\Component\Mailer\MailerInterface;

class MailerManager
{
    private MailerInterface $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }


}