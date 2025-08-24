<?php

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {

        $exceptions->renderable(function (ModelNotFoundException $e, Request $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'data' => [
                        'message' => 'Entity not found',
                        'status' => 404
                    ]
                ], 404);
            }
        });

        $exceptions->renderable(function (NotFoundHttpException $e, Request $request) {
            $previous = $e->getPrevious();

            if ($previous instanceof ModelNotFoundException) {
                if ($request->expectsJson() || $request->is('api/*')) {
                    return response()->json([
                        'data' => [
                            'message' => 'Entity not found',
                            'status' => 404
                        ]
                    ], 404);
                }
            } else {
                if ($request->expectsJson() || $request->is('api/*')) {
                    return response()->json([
                        'data' => [
                            'message' => 'Endpoint not found',
                            'status' => 404
                        ]
                    ], 404);
                }
            }
        });

    })->create();
