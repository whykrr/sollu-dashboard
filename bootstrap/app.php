<?php

use App\Exceptions\ExceptionHandler;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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
            'plan.feature' => \App\Http\Middleware\CheckPlanFeature::class,
        ]);
    })
    ->withExceptions(new ExceptionHandler)
    ->create();
