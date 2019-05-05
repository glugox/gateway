<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Laravel\Lumen\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Http\Response;

class Handler extends ExceptionHandler {

    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        AuthorizationException::class,
        HttpException::class,
        ModelNotFoundException::class,
        ValidationException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Exception $exception) {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     */
    public function render($request, Exception $exception) {
        if ($exception instanceof UnableToExecuteRequestException) {
            return new Response(json_encode(['errors' => [$exception->getMessage()]]), $exception->getCode());
        }
        if ($exception instanceof NotFoundHttpException) {
            return new Response(json_encode(['errors' => ['Resource not found']]), 404);
        }
        if ($exception instanceof ServiceNotFoundException) {
            return new Response(json_encode(['errors' => [$exception->getMessage()]]), 404);
        }

        var_dump($exception);
        die("");
        
        return new Response(json_encode(['errors' => [$exception->getFile(), $exception->getTrace(), $exception->getMessage()]]), 404);

        
    }

}
