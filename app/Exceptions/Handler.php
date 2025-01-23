<?php

namespace App\Exceptions;

use Throwable;
use App\Traits\HttpResponses;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class Handler extends ExceptionHandler
{
    use HttpResponses;

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->renderable(function (Throwable $e) {
            if (request()->expectsJson()) {
                if ($e instanceof ModelNotFoundException) {
                    return $this->error('Record not found.', [], 404);
                }

                if ($e instanceof AuthenticationException) {
                    return $this->error('Unauthenticated.', [], 401);
                }

                if ($e instanceof AuthorizationException) {
                    return $this->error('This action is unauthorized.', [], 403);
                }

                if ($e instanceof ValidationException) {
                    return $this->error('Validation errors.', $e->errors(), 422);
                }

                if ($e instanceof HttpException) {
                    return $this->error($e->getMessage(), [], $e->getStatusCode());
                }

                // Handle other exceptions
                if (config('app.debug')) {
                    return $this->error($e->getMessage(), [], 500);
                }

                return $this->error('Server Error.', [], 500);
            }
        });
    }
} 