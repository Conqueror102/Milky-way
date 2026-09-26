<?php

namespace App\Providers;

use App\Models\User;
use App\Payments\PaymentGateway;
use App\Payments\PaystackGateway;
use App\Services\Cloudinary;
use App\Support\SiteContent;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
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

        $this->app->singleton(SiteContent::class);

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

        Gate::define('admin', fn (User $user): bool => $user->is_admin);

        // The home page's editable copy and photos, as $site in every view. It only
        // reads the database the first time a view asks for something.
        View::share('site', $this->app->make(SiteContent::class));
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
