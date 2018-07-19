<?php

namespace App\Providers;

use App\Service\Contracts\ICurrencyService;
use App\Service\Contracts\IMarketService;
use App\Service\Contracts\INotificationService;
use App\Service\Contracts\IWalletService;
use App\Service\CurrencyService;
use App\Service\MarketService;
use App\Service\NotificationService;
use App\Service\Validators\Contracts\IMarketValidator;
use App\Service\Validators\Contracts\IWalletValidator;
use App\Service\Validators\MarketValidator;
use App\Service\Validators\WalletValidator;
use App\Service\WalletService;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);

        $this->app->bind(IWalletValidator::class,WalletValidator::class);
        $this->app->bind( IMarketValidator::class, MarketValidator::class);

        $this->app->bind(ICurrencyService::class, CurrencyService::class);
        $this->app->bind(IWalletService::class, WalletService::class);
        $this->app->bind(IMarketService::class, MarketService::class);
        $this->app->bind(INotificationService::class, NotificationService::class);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
