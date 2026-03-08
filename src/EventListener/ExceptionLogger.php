<?php

namespace App\EventListener;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;

class ExceptionListener
{
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function onKernelException(ExceptionEvent $event)
    {
        $exception = $event->getThrowable();

        $this->logger->critical('Unexpected error occurred', [
            'exception' => $exception,
        ]);

         $response = new Response();
         $response->setContent('Unexpected error occurred.');
         $response->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR);
 
         $event->setResponse($response);
    }
}