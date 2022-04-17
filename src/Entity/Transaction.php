<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\TransactionRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TransactionRepository::class)
 */
class Transaction
{
    use IdTrait;
    use CreatedAtTrait;

    /**
     * @ORM\ManyToOne(targetEntity=User::class)
     */
    private User $senderUser;

    /**
     * @ORM\ManyToOne(targetEntity=User::class)
     */
    private User $recipientUser;

    /**
     * @ORM\Column(type="integer")
     */
    private int $sum;

    public static function create(
        User $senderUser,
        User $recipientUser,
        int $sum
    ): self {
        return (new static())
            ->setSenderUser($senderUser)
            ->setRecipientUser($recipientUser)
            ->setSum($sum)
        ;
    }

    public function getSenderUser(): User
    {
        return $this->senderUser;
    }

    public function setSenderUser(User $senderUser): self
    {
        $this->senderUser = $senderUser;

        return $this;
    }

    public function getRecipientUser(): User
    {
        return $this->recipientUser;
    }

    public function setRecipientUser(User $recipientUser): self
    {
        $this->recipientUser = $recipientUser;

        return $this;
    }

    public function getSum(): int
    {
        return $this->sum;
    }

    public function setSum(int $sum): self
    {
        $this->sum = $sum;

        return $this;
    }
}
