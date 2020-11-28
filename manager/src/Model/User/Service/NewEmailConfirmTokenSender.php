<?php

declare(strict_types=1);

namespace App\Model\User\Service;

use App\Model\User\Entity\User\Email;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mime\Email as MimeEmail;
use Twig\Environment;

class NewEmailConfirmTokenSender
{
    private $mailer;
    private $twig;

    public function __construct(Mailer $mailer, Environment $twig)
    {
        $this->mailer = $mailer;
        $this->twig = $twig;
    }

    public function send(Email $email, string $token): void
    {
        $message = (new MimeEmail())
            ->to($email->getValue())
            ->subject('Email confirmation')
            ->text($this->twig->render('mail/user/email.html.twig', [
                'token' => $token
            ]), 'text/html');

        try {
            $this->mailer->send($message);
        } catch (\DomainException $e) {
            throw new \RuntimeException('Unable to send message!');
        }
    }
}