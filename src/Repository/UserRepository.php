<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\User;
use App\Exception\Entity\NotFound\UserNotFoundException;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function add(User $entity, bool $flush = true): void
    {
        $this->_em->persist($entity);

        if ($flush) {
            $this->_em->flush();
        }
    }

    public function remove(User $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);

        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @param int $id
     * @param int|null $lockMode
     * @param int|null $lockVersion
     * @param bool $throwException
     * @return User|null
     * @throws UserNotFoundException
     */
    public function findById(
        int $id,
        ?int $lockMode = null,
        ?int $lockVersion = null,
        bool $throwException = true
    ): ?User {
        $entity = $this->find($id, $lockMode, $lockVersion);

        if (is_null($entity) && $throwException) {
            throw new UserNotFoundException($entity);
        }

        return $entity;
    }
}
