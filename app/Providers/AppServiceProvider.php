<?php

namespace App\Providers;

use App\Models\User;
use App\Payments\PaymentGateway;
use App\Payments\PaystackGateway;
use App\Services\Cloudinary;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;
use RuntimeException;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(Cloudinary::class, fn (): Cloudinary => Cloudinary::fromConfig());

        $this->app->singleton(PaystackGateway::class, fn () => new PaystackGateway(
            (string) config('services.paystack.secret_key'),
            (string) config('services.paystack.base_url'),
        ));

        $this->app->bind(PaymentGateway::class, fn () => match (config('milkyway.shop.payment_gateway')) {
            'paystack' => $this->app->make(PaystackGateway::class),
            default => throw new RuntimeException('No payment gateway is configured.'),
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureDefaults();

        Gate::define('admin', fn (User $user): bool => $user->isAdmin());
    }

    /**
     * Configure default behaviors for production-ready applications.
     */
    protected function configureDefaults(): void
    {
        Date::use(CarbonImmutable::class);

        DB::prohibitDestructiveCommands(
            app()->isProduction(),
        );

        Password::defaults(fn (): ?Password => app()->isProduction()
            ? Password::min(12)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->uncompromised()
            : null,
        );
    }
}
