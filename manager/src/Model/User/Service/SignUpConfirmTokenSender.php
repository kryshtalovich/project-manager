<?php

declare(strict_types=1);

namespace App\Model\User\Service;

use App\Model\User\Entity\User\Email;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email as MimeEmail;
use Twig\Environment;

class SignUpConfirmTokenSender
{
    private $mailer;
    private $twig;
    private $from;

    public function __construct(MailerInterface $mailer, Environment $twig, array $from)
    {
        $this->mailer = $mailer;
        $this->twig = $twig;
        $this->from = $from;
    }
    public function send(Email $email, string $token): void
    {
        $message = (new MimeEmail())
            ->from(key($this->from))
            ->to($email->getValue())
            ->subject('Sign up confirmation')
            ->text($this->twig->render('mail/user/signup.html.twig', [
                'token' => $token
            ]), 'text/html');

        $this->mailer->send($message);
//        if (!$this->mailer->send($message)){
//            throw new \RuntimeException('Unable to send message!');
//        }
    }
}