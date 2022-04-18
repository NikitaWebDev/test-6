<?php

declare(strict_types=1);

namespace App\Tests\Integration\Service;

use App\Entity\User;
use App\Service\TransactionService;
use App\Repository\UserRepository;
use App\DataFixtures\UserFixture;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class TransactionServiceTest extends KernelTestCase
{
    /** @var TransactionService|object */
    private object $transactionService;

    /** @var object|UserRepository */
    private object $userRepository;

    public function setUp(): void
    {
        $container = static::getContainer();

        $this->transactionService = $container->get(TransactionService::class);
        $this->userRepository = $container->get(UserRepository::class);

        parent::setUp();
    }

    /**
     * @return void
     * @throws \App\Exception\Entity\NotFound\UserNotFoundException
     * @throws \Doctrine\DBAL\Exception
     * @throws \Throwable
     */
    public function testCreateSucceeded(): void
    {
        // Init data
        self::bootKernel();

        $senderUser = $this->getSenderUser();
        $senderUserId = $senderUser->getId();

        $recipientUser = $this->getRecipientUser();
        $recipientUserId = $recipientUser->getId();

        $sum = $senderUser->getBalance();

        $expectedBalanceSenderUser = 0;
        $expectedBalanceRecipientUser = $recipientUser->getBalance() + $sum;

        // Execute
        $transaction = $this->transactionService->create(
            $senderUserId,
            $recipientUserId,
            $sum
        );

        // Assert
        static::assertSame(
            $senderUserId,
            $transaction->getSenderUser()->getId()
        );
        static::assertSame(
            $recipientUserId,
            $transaction->getRecipientUser()->getId()
        );
        static::assertSame($sum, $transaction->getSum());

        $senderUser = $this->getSenderUser();
        static::assertSame(
            $expectedBalanceSenderUser,
            $senderUser->getBalance()
        );

        $recipientUser = $this->getRecipientUser();
        static::assertSame(
            $expectedBalanceRecipientUser,
            $recipientUser->getBalance()
        );
    }

    public function testCreateFailed(): void
    {
        // Init data
        self::bootKernel();

        $senderUser = $this->getSenderUser();
        $expectedBalanceSenderUser = $senderUser->getBalance();

        $recipientUser = $this->getRecipientUser();
        $expectedBalanceRecipientUser = $recipientUser->getBalance();

        $sum = $senderUser->getBalance() + 100;

        // Execute
        try {
            $this->transactionService->create(
                $senderUser->getId(),
                $recipientUser->getId(),
                $sum
            );
        } catch (\Throwable $exception) {
            static::assertSame(\LogicException::class, \get_class($exception));
            static::assertSame(
                'Недостаточно средств на счете!',
                $exception->getMessage()
            );

            $senderUser = $this->getSenderUser();
            static::assertSame(
                $expectedBalanceSenderUser,
                $senderUser->getBalance()
            );

            $recipientUser = $this->getRecipientUser();
            static::assertSame(
                $expectedBalanceRecipientUser,
                $recipientUser->getBalance()
            );
        }
    }

    private function getSenderUser(): User
    {
        return $this->userRepository->findOneByName(UserFixture::USER_1_NAME);
    }

    private function getRecipientUser(): User
    {
        return $this->userRepository->findOneByName(UserFixture::USER_2_NAME);
    }
}
