<?php

declare(strict_types=1);

namespace App\Controller;

use App\Exception\Entity\NotFound\EntityNotFoundException;
use App\Service\TransactionService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{JsonResponse, Request};
use Symfony\Component\Routing\Annotation\Route;

class TransactionController extends AbstractController
{
    /**
     * Принимает POST-параметры: int senderUserId, int recipientUserId, int sum.
     *
     * @Route("/api/transactions", name="transaction_create", methods={"POST"})
     */
    public function create(
        Request $request,
        TransactionService $transactionService
    ): JsonResponse {
        $senderUserId = $request->get('senderUserId');
        try {
            $senderUserId = $this->prepareUserId($senderUserId, 'sender');
        } catch (\Exception $exception) {
            return $this->handleException($exception);
        }

        $recipientUserId = $request->get('recipientUserId');
        try {
            $recipientUserId = $this->prepareUserId($recipientUserId, 'recipient');
        } catch (\Exception $exception) {
            return $this->handleException($exception);
        }

        $sum = $request->get('sum');
        try {
            $sum = $this->prepareSum($sum);
        } catch (\Exception $exception) {
            return $this->handleException($exception);
        }

        try {
            $transaction = $transactionService->create(
                $senderUserId,
                $recipientUserId,
                $sum
            );
        } catch (EntityNotFoundException $exception) {
            return $this->json([
                'message' => $exception->getMessage(),
            ], JsonResponse::HTTP_NOT_FOUND);
        } catch (\LogicException $exception) {
            return $this->json([
                'message' => $exception->getMessage(),
            ], JsonResponse::HTTP_BAD_REQUEST);
        } catch (\Throwable $exception) {
            return $this->json([
                'message' => $exception->getMessage(),
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $this->json([
            'id' => $transaction->getId(),
        ], JsonResponse::HTTP_CREATED);
    }

    /**
     * @param mixed $userId
     * @param string $type sender/recipient
     * @return int
     * @throws \UnexpectedValueException
     * @throws \InvalidArgumentException
     */
    private function prepareUserId($userId, string $type): int
    {
        switch ($type) {
            case 'sender':
                $displayType = 'отправителя';
                break;
            case 'recipient':
                $displayType = 'получателя';
                break;
            default:
                throw new \UnexpectedValueException('Неправильный параметр type!', 500);
        }

        if (\is_null($userId)) {
            throw new \InvalidArgumentException("Не указан ID $displayType!");
        }
        if (!\is_numeric($userId)) {
            throw new \InvalidArgumentException("Указан некорректный ID $displayType!");
        }

        $userId = (int) $userId;
        if ($userId <= 0) {
            throw new \UnexpectedValueException("Указан некорректный ID $displayType!");
        }

        return $userId;
    }

    /**
     * @param mixed $sum
     * @return int
     * @throws \UnexpectedValueException
     * @throws \InvalidArgumentException
     */
    private function prepareSum($sum): int
    {
        if (\is_null($sum)) {
            throw new \InvalidArgumentException('Не указана сумма!');
        }
        if (!\is_numeric($sum)) {
            throw new \InvalidArgumentException('Указана некорректная сумма!');
        }

        $sum = (int) $sum;
        if ($sum <= 0) {
            throw new \UnexpectedValueException('Указана некорректная сумма!');
        }

        return $sum;
    }

    private function handleException(\Exception $exception): JsonResponse
    {
        if ($code = $exception->getCode()) {
            return $this->json([
                'message' => $exception->getMessage(),
            ], $code);
        }

        return $this->json([
            'message' => $exception->getMessage(),
        ], JsonResponse::HTTP_BAD_REQUEST);
    }
}
