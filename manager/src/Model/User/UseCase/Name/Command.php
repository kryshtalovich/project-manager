<?php

declare(strict_types=1);

namespace App\Model\User\UseCase\Name;

use Symfony\Component\Validator\Constraint as Assert;

class Command
{
    /**
     * @var string
     * @Assert\NotBlank
     */
    public $id;
    /**
     * @var string
     * @Assert\NotBlank
     */
    public $firstName;
    /**
     * @var string
     * @Assert\NotBlank
     */
    public $lastName;

    public function __construct($id)
    {
        $this->id = $id;
    }
}