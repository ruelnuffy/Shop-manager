<?php

use App\Http\Middleware\EnsureUserIsSuperAdmin;
use App\Http\Middleware\ResolveTenant;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Session\Middleware\StartSession;

// Ensure required storage directories exist (prevents InvalidArgumentException: Please provide a valid cache path)
foreach ([
    dirname(__DIR__).'/storage/framework/views',
    dirname(__DIR__).'/storage/framework/sessions',
    dirname(__DIR__).'/storage/framework/cache/data',
    dirname(__DIR__).'/storage/app/public',
    dirname(__DIR__).'/storage/logs',
    dirname(__DIR__).'/bootstrap/cache',
] as $dir) {
    if (! is_dir($dir)) {
        @mkdir($dir, 0775, true);
    }
}

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->validateCsrfTokens(except: [
            'paystack/webhook',
        ]);

        $middleware->web(append: [
            ResolveTenant::class,
        ]);

        $middleware->appendToPriorityList(
            StartSession::class,
            ResolveTenant::class,
        );

        $middleware->alias([
            'super_admin' => EnsureUserIsSuperAdmin::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
    })->create();
