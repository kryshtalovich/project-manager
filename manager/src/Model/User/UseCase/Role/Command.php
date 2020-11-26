<?php

declare(strict_types=1);

namespace App\Model\User\UseCase\Role;

class Command
{
    /**
     * @var string
     */
    public $id;
    /**
     * @var string
     */
    public $role;

    public function __construct(string $id)
    {
        $this->id = $id;
    }
}