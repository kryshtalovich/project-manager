<?php

declare(strict_types=1);

namespace App\Model\User\Entity\Service;


use Ramsey\Uuid\Uuid;

class ConfirmTokenizer
{
    public function generate(): string
    {
        return Uuid::uuid4()->toString();
    }
}