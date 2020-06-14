<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param  \Throwable  $exception
     * @return void
     *
     * @throws \Exception
     */
    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $e)
    {
        //return parent::render($request, $exception);
        $trace = $e->getTraceAsString();
        if ($e instanceof ModelNotFoundException
            && mb_strpos($trace, 'app\Http\Controllers\WelcomeController')
        ) {
            return response()->json('Exception ' . $e->getMessage());
        } elseif ($e instanceof ModelNotFoundException) {
            return "General model not found";
        } elseif ($e instanceof \Illuminate\Auth\AuthenticationException) {
            return "AuthenticationException";
        } elseif ($e instanceof \Illuminate\Validation\ValidationException) {
            return "ValidationException";
        } elseif ($e instanceof \Symfony\Component\HttpKernel\Exception\HttpException) {
            return "HttpException";
        } elseif ($e instanceof \Illuminate\Auth\Access\AuthorizationException) {
            return "AuthorizationException";
        }   
    
        return parent::render($request, $e);
    }
}
