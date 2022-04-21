<?php

declare(strict_types=1);

namespace App\Controller;

use App\Exception\Entity\NotFound\EntityNotFoundException;
use App\Service\TransactionService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{JsonResponse, Request};
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\{Validation, ConstraintViolationInterface, ConstraintViolationListInterface};
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Constraints\{NotBlank, Type, GreaterThan};

class TransactionController extends AbstractController
{
    private array $validateErrors = ['errors' => []];
    private ValidatorInterface $validator;

    public function __construct()
    {
        $this->validator = Validation::createValidator();
    }

    /**
     * Принимает POST-параметры: int senderUserId, int recipientUserId, int sum.
     *
     * @Route("/api/transactions", name="transaction_create", methods={"POST"})
     */
    public function create(
        Request $request,
        TransactionService $transactionService
    ): JsonResponse {
        $this->validate($request);
        if (!\empty($this->validateErrors['errors'])) {
            return $this->json(
                $this->validateErrors,
                JsonResponse::HTTP_BAD_REQUEST
            );
        }

        $senderUserId = (int) $request->get('senderUserId');
        $recipientUserId = (int) $request->get('recipientUserId');
        $sum = (int) $request->get('sum');

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

    private function validate(Request $request): void
    {
        $this->validateValue($request->get('senderUserId'));
        $this->validateValue($request->get('recipientUserId'));
        $this->validateValue($request->get('sum'));
    }

    /**
     * @param mixed $value
     * @return void
     */
    private function validateValue($value): void
    {
        $violations = $this->validator->validate($value, [
            new NotBlank(),
            new Type('numeric'),
            new GreaterThan(0),
        ]);
        $this->handleViolations($violations);
    }

    private function handleViolations(ConstraintViolationListInterface $violations): void
    {
        if (0 === count($violations)) {
            return;
        }

        foreach ($violations as $violation) {
            /** @var ConstraintViolationInterface $violation */
            $this->validateErrors['errors'][] = [
                'status' => JsonResponse::HTTP_BAD_REQUEST,
                'title' => $violation->getMessage(),
                'source' => [
                    'pointer' => "/data/attributes/{$violation->getPropertyPath()}",
                ],
            ];
        }
    }
}
