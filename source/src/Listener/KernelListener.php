<?php

namespace App\Listener;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Throwable;

class KernelListener
{
    /**
     * @throws Throwable
     */
    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        $event->setResponse(
            (new JsonResponse())->setStatusCode($exception->getCode() ?: 400)->setData(
                [
                    'status' => 'error',
                    'message' => $exception->getMessage(),
                ]
            )
        );
    }
}
