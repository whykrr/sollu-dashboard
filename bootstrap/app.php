<?php

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
        then: function () {
            $appDomain = config('domain.app', 'app.sollu.test');
            $cockpitDomain = config('domain.cockpit', 'cockpit.sollu.test');
            $apiDomain = config('domain.api', 'api.sollu.test');

            Route::middleware(['web', \App\Http\Middleware\HandleAppInertiaRequests::class])
                ->domain($appDomain)
                ->group(base_path('routes/app.php'));

            Route::middleware(['web', \App\Http\Middleware\HandleCockpitInertiaRequests::class])
                ->domain($cockpitDomain)
                ->group(base_path('routes/cockpit.php'));

            Route::middleware('api')
                ->domain($apiDomain)
                ->group(base_path('routes/api.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->trustProxies(at: '*');

        $middleware->web(prepend: [
            \App\Http\Middleware\ConfigureDomainSession::class,
        ]);

        $middleware->web(append: [
            \Illuminate\Cookie\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
            // \Illuminate\Session\Middleware\AuthenticateSession::class,
        ]);

        $middleware->validateCsrfTokens(except: [
            'api/*',
        ]);

        $middleware->redirectGuestsTo(function (Request $request) {
            if (str_starts_with($request->getHost(), 'cockpit.') || $request->getHost() === config('domain.cockpit')) {
                return route('cockpit.login');
            }

            return route('login');
        });

        $middleware->redirectUsersTo(function (Request $request) {
            if (str_starts_with($request->getHost(), 'cockpit.') || $request->getHost() === config('domain.cockpit')) {
                return route('cockpit.dashboard');
            }

            return route('overview');
        });

        $middleware->alias([
            'stock.not.frozen' => \App\Http\Middleware\EnsureStockNotFrozen::class,
            'pos.device' => \App\Http\Middleware\VerifyPosDevice::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->shouldRenderJsonWhen(function (Request $request, Throwable $e) {
            if ($request->is('api/*') || $request->getHost() === config('domain.api')) {
                return true;
            }

            return $request->expectsJson();
        });

        $exceptions->report(function (Throwable $e) {
            Log::error($e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);
        });

        // Error Authorization
        $exceptions->renderable(function (AccessDeniedHttpException $e, Request $request) {
            if ($request->expectsJson()) {
                return response()->json(['message' => $e->getMessage()], Response::HTTP_FORBIDDEN);
            }

            return redirect()->back()->with('failed', $e->getMessage());
        });

        // Error DB
        $exceptions->renderable(function (QueryException $e, Request $request) {
            $message = 'Terjadi kesalahan database. coba lagi nanti.';
            if ($request->expectsJson()) {
                return response()->json(['message' => $message], Response::HTTP_INTERNAL_SERVER_ERROR);
            }

            return redirect()->back()->with('failed', $message);
        });

        // Error Data Not Found
        $exceptions->renderable(function (ModelNotFoundException $e, Request $request) {
            $message = 'Data tidak ditemukan.';
            if ($request->expectsJson()) {
                return response()->json(['message' => $message], Response::HTTP_NOT_FOUND);
            }

            return redirect()->back()->with('failed', $message);
        });

        // Error Page Not Found
        $exceptions->renderable(function (NotFoundHttpException $e, Request $request) {
            $message = 'Halaman tidak ditemukan.';
            if ($request->expectsJson()) {
                return response()->json(['message' => $message], Response::HTTP_NOT_FOUND);
            }

            return redirect()->back()->with('failed', $message);
        });

        // Error Throttle
        $exceptions->renderable(function (ThrottleRequestsException $e, Request $request) {
            $message = 'Terlalu banyak permintaan. Coba lagi nanti.';
            if ($request->expectsJson()) {
                return response()->json(['message' => $message], Response::HTTP_TOO_MANY_REQUESTS);
            }

            // if page for guest
            if ($request->is('login') || $request->is('register') || $request->is('forgot')) {
                throw ValidationException::withMessages([
                    'email' => $message,
                ]);
            }

            return redirect()->back()->with('failed', $message);
        });

    })->create();
