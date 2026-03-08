<?php

namespace App\EventListener;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\Validator\Exception\ValidationFailedException;

class ApiValidationListener
{
    public function onKernelException(ExceptionEvent $event): void
    {
        $e = $event->getThrowable();

        if ($e instanceof ValidationFailedException) {
            $errors = [];

            foreach ($e->getViolations() as $violation) {
                $errors[] = [
                    'error' => $violation->getMessage(),
                ];
            }

            $event->setResponse(new JsonResponse($errors, 405));
            $event->stopPropagation();
        }
    }
}