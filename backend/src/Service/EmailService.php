<?php
namespace App\Service;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class EmailService
{
    private MailerInterface $mailer;
    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function sendEmail(string $to, string $subject, string $body): void
    {
        $email = (new Email())
            ->from('no-reply@ytq.pl')
            ->to(getenv('TEST_EMAIL') ?: $to)
            ->subject($subject)
            ->html($body);

        $this->mailer->send($email);

    }
}
