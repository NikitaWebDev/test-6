<?php

declare(strict_types=1);

namespace App\Exception\Entity\NotFound;

use App\Entity\User;

class UserNotFoundException extends EntityNotFoundException
{
    public function __construct(
        User $user,
        ?string $message = null,
        int $code = 0,
        \Throwable $previous = null
    ) {
        parent::__construct($user, $message, $code, $previous);
    }

    protected function buildMessage(): string
    {
        return "Пользователь с id={$this->entity->getId()} не найден!";
    }
}
