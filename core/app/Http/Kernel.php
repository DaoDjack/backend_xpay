<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;



//Enretgistrement des routes
session_start();
$url = $_SERVER['REQUEST_URI'];
$url = explode('?',$url);
$_SESSION['URI'] = explode('/',$url[0]);

class Kernel extends HttpKernel
{
    /**
     * The application's global HTTP middleware stack.
     *
     * These middleware are run during every request to your application.
     *
     * @var array<int, class-string|string>
     */
    protected $middleware = [
        // \App\Http\Middleware\TrustHosts::class,
        \App\Http\Middleware\TrustProxies::class,
        \Illuminate\Http\Middleware\HandleCors::class,
        \App\Http\Middleware\PreventRequestsDuringMaintenance::class,
        \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
        \App\Http\Middleware\TrimStrings::class,
        \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
    ];

    /**
     * The application's route middleware groups.
     *
     * @var array<string, array<int, class-string|string>>
     */
    protected $middlewareGroups = [
        'web' => [
            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\Session\Middleware\AuthenticateSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
            \App\Http\Middleware\LanguageMiddleware::class,

            \App\Http\Middleware\Logout::class,
        ],

        'api' => [
            // \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
            'throttle:api',
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],
    ];

    /**
     * The application's route middleware.
     *
     * These middleware may be assigned to groups or used individually.
     *
     * @var array<string, class-string|string>
     */
    protected $routeMiddleware = [
        'auth' => \App\Http\Middleware\Authenticate::class,
        'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'cache.headers' => \Illuminate\Http\Middleware\SetCacheHeaders::class,
        'can' => \Illuminate\Auth\Middleware\Authorize::class,
        'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
        'password.confirm' => \Illuminate\Auth\Middleware\RequirePassword::class,
        'signed' => \Illuminate\Routing\Middleware\ValidateSignature::class,
      
        'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,

        'admin' => \App\Http\Middleware\RedirectIfNotAdmin::class, 
        'admin.guest' => \App\Http\Middleware\RedirectIfAdmin::class,

        'agent' => \App\Http\Middleware\RedirectIfNotAgent::class,
        'agent.guest' => \App\Http\Middleware\RedirectIfAgent::class, 

        'merchant' => \App\Http\Middleware\RedirectIfNotMerchant::class,
        'merchant.guest' => \App\Http\Middleware\RedirectIfMerchant::class,

        'registration.status' => \App\Http\Middleware\AllowRegistration::class,
        'check.status' => \App\Http\Middleware\CheckStatus::class,

        'demo' => \App\Http\Middleware\Demo::class,
        'kyc' => \App\Http\Middleware\KycMiddleware::class,

        'registration.complete' => \App\Http\Middleware\RegistrationStep::class,
        'maintenance' => \App\Http\Middleware\MaintenanceMode::class,
        'module' => \App\Http\Middleware\Module::class,

        'abilities' => \Laravel\Sanctum\Http\Middleware\CheckAbilities::class,
        'ability' => \Laravel\Sanctum\Http\Middleware\CheckForAnyAbility::class,
    ];
}
