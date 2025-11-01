<?php

namespace App\EventListener;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\Validator\Exception\ValidationFailedException;

class ExceptionListener
{
    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        if ($exception instanceof ValidationFailedException) {
            $violations = [];
            foreach ($exception->getViolations() as $violation) {
                $violations[] = [
                    'field' => $violation->getPropertyPath(),
                    'message' => $violation->getMessage(),
                ];
            }

            $event->setResponse(new JsonResponse([
                'status' => 'error',
                'errors' => $violations,
            ], JsonResponse::HTTP_BAD_REQUEST));

            return;
        }

        if ($exception instanceof HttpExceptionInterface) {
            $response = new JsonResponse([
                'status' => 'error',
                'message' => $exception->getMessage(),
            ], $exception->getStatusCode());
        } else {
            $response = new JsonResponse([
                'status' => 'error',
                'message' => 'Internal Server Error',
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }

        $event->setResponse($response);
    }
}
