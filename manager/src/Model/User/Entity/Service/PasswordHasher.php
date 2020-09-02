<?php


namespace App\Model\User\Entity\Service;

class PasswordHasher
{
    public function hash(string $password): string
    {
        $hash = password_hash($password, PASSWORD_ARGON2ID);
        if ($hash === false) {
            throw new \RuntimeException('Unable to generate hash');
        }
        return $hash;
    }
}