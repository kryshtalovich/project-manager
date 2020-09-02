<?php

declare(strict_types=1);

namespace App\User\Model\Service;

use App\Model\User\Entity\User\Email;

interface ConfirmTokenSender
{
    public function send(Email $email, string $token): void;
}