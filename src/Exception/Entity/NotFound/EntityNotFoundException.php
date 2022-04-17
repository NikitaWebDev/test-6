<?php

declare(strict_types=1);

namespace App\Exception\Entity\NotFound;

class EntityNotFoundException extends \Exception
{
    protected object $entity;

    public function __construct(
        object $entity,
        ?string $message = null,
        int $code = 0,
        \Throwable $previous = null
    ) {
        $this->entity = $entity;

        if (is_null($message)) {
            $message = $this->buildMessage();
        }

        parent::__construct($message, $code, $previous);
    }

    protected function buildMessage(): string
    {
        return 'Сущность ' . \get_class($this->entity) . " с id={$this->entity->getId()} не найдена!";
    }
}
