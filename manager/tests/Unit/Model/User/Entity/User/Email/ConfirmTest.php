<?php

declare(strict_types=1);

namespace App\Tests\Unit\Model\User\Entity\User\Email;

use App\Model\User\Entity\User\Email;
use App\Tests\Builder\User\UserBuilder;
use Monolog\Test\TestCase;

class ConfirmTest extends TestCase
{
    public function testSuccess(): void
    {
        $user = (new UserBuilder())->viaEmail()->confirmed()->build();

        $user->requestEmailChanging(
            $email = new Email('new@app.test'),
            $token = 'token'
        );

        $user->confirmEmailChanging($token);

        self::assertEquals($email, $user->getEmail());
        self::assertNull($user->getNewEmailToken());
        self::assertNull($user->getNewEmail());
    }

    public function testNotRequested(): void
    {
        $user = (new UserBuilder())->viaEmail()->confirmed()->build();

        $this->expectExceptionMessage('Changing is not requested.');
        $user->requestEmailChanging('token');
    }

    public function testIncorrect(): void
    {
        $user = (new UserBuilder())->viaEmail()->confirmed()->build();

        $user->requestEmailChanging(
            $email = new Email('new@app.test'),
            $token = 'token'
        );

        $this->expectExceptionMessage('Incorrect changing token.');
        $user->requestEmailChanging('incorrect-token');
    }
}