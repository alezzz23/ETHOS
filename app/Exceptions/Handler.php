<?php

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            // Delegate to default logger channels; custom reporting can go here.
        });

        // Uniform JSON error envelopes for API / expectsJson clients.
        $this->renderable(function (Throwable $e, Request $request) {
            if (! $request->expectsJson()) {
                return null;
            }

            return $this->renderJsonException($e);
        });
    }

    protected function renderJsonException(Throwable $e): JsonResponse
    {
        [$status, $code] = $this->mapExceptionToStatusCode($e);

        $payload = [
            'error' => [
                'code'    => $code,
                'message' => $this->safeMessage($e, $status),
            ],
        ];

        if ($e instanceof ValidationException) {
            $payload['error']['details'] = $e->errors();
        }

        if (config('app.debug') && ! $e instanceof ValidationException) {
            $payload['error']['debug'] = [
                'exception' => get_class($e),
                'file'      => $e->getFile(),
                'line'      => $e->getLine(),
            ];
        }

        return response()->json($payload, $status);
    }

    /**
     * @return array{0:int,1:string}
     */
    protected function mapExceptionToStatusCode(Throwable $e): array
    {
        return match (true) {
            $e instanceof ValidationException         => [422, 'validation_failed'],
            $e instanceof AuthenticationException     => [401, 'unauthenticated'],
            $e instanceof AuthorizationException      => [403, 'forbidden'],
            $e instanceof ModelNotFoundException,
            $e instanceof NotFoundHttpException       => [404, 'not_found'],
            $e instanceof TooManyRequestsHttpException => [429, 'rate_limited'],
            $e instanceof HttpExceptionInterface      => [$e->getStatusCode(), 'http_error'],
            default                                    => [500, 'server_error'],
        };
    }

    protected function safeMessage(Throwable $e, int $status): string
    {
        // Never leak stack traces or SQL detail in prod.
        if ($status >= 500 && ! config('app.debug')) {
            return 'Ha ocurrido un error inesperado. Intenta de nuevo más tarde.';
        }

        return $e->getMessage() !== '' ? $e->getMessage() : 'Error';
    }
}

