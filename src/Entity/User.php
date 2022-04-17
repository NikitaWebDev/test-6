<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use DateTimeInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @ORM\Table(name="`user`")
 */
class User
{
    use IdTrait;
    use CreatedAtTrait;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private string $name;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private ?DateTimeInterface $updatedAt = null;

    /**
     * @Assert\GreaterThanOrEqual(
     *     value = 0,
     *     message="Баланс не может быть меньше 0!"
     * )
     *
     * @ORM\Column(type="integer", options={"default" : 0})
     */
    private int $balance = 0;

    public static function create(string $name, int $balance = 0): self
    {
        return (new static())->setName($name)->setBalance($balance);
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getUpdatedAt(): ?DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getBalance(): int
    {
        return $this->balance;
    }

    public function setBalance(int $balance): self
    {
        $this->balance = $balance;

        return $this;
    }
}
