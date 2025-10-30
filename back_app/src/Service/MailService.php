<?php

namespace App\Service;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;

class MailService
{
    public function __construct(private MailerInterface $mailer)
    {
    }

    public function sendEmail(
        string $to,
        string $subject,
        string $htmlContent,
        ?string $textContent = null,
        ?string $from = 'rahhoutioualid@example.com',
        ?string $fromName = 'Invoice App',
        array $cc = [],
        array $bcc = [],
        array $attachments = []
    ): void {
        $email = (new Email())
            ->from(new Address($from, $fromName))
            ->to($to)
            ->subject($subject)
            ->html($htmlContent)
            ->text($textContent ?? strip_tags($htmlContent));

        // Add optional CC and BCC
        if (!empty($cc)) {
            $email->cc(...$cc);
        }
        if (!empty($bcc)) {
            $email->bcc(...$bcc);
        }

        // Add attachments if any
        foreach ($attachments as $filePath) {
            $email->attachFromPath($filePath);
        }
        
        $this->mailer->send($email);
    }
}
