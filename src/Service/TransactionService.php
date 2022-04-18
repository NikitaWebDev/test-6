<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Transaction;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\DBAL\LockMode;

class TransactionService
{
    private EntityManagerInterface $em;
    private UserRepository $userRepository;

    public function __construct(
        EntityManagerInterface $em,
        UserRepository $userRepository
    ) {
        $this->em = $em;
        $this->userRepository = $userRepository;
    }

    /**
     * Создает транзакцию и записывает в БД.
     *
     * @param int $senderUserId
     * @param int $recipientUserId
     * @param int $sum
     * @return Transaction
     * @throws \App\Exception\Entity\NotFound\UserNotFoundException
     * @throws \LogicException
     * @throws \Doctrine\DBAL\Exception
     * @throws \Throwable
     */
    public function create(
        int $senderUserId,
        int $recipientUserId,
        int $sum
    ): Transaction {
        $connection = $this->em->getConnection();

        try {
            $connection->beginTransaction();

            // получаем пользователей
            $senderUser = $this->userRepository->findById(
                $senderUserId,
                LockMode::PESSIMISTIC_WRITE
            );
            $recipientUser = $this->userRepository->findById(
                $recipientUserId,
                LockMode::PESSIMISTIC_WRITE
            );

            // вычисляем баланс отправителя
            $balanceSenderUser = $senderUser->getBalance();
            if ($sum > $balanceSenderUser) {
                throw new \LogicException('Недостаточно средств на счете!');
            }
            $balanceSenderUser -= $sum;
            $senderUser->setBalance($balanceSenderUser);
            $this->em->persist($senderUser);

            // вычисляем баланс получателя
            $balanceRecipientUser = $recipientUser->getBalance() + $sum;
            $recipientUser->setBalance($balanceRecipientUser);
            $this->em->persist($recipientUser);

            // создаем транзакцию
            $transaction = Transaction::create($senderUser, $recipientUser, $sum);
            $this->em->persist($transaction);

            // выполняем операцию
            $this->em->flush();
            $connection->commit();
        } catch (\Throwable $exception) {
            $connection->rollBack();

            throw $exception;
        }

        return $transaction;
    }
}
