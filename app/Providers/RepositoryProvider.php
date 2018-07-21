<?php

namespace App\Providers;

use App\Repository\Contracts\ICurrencyRepository;
use App\Repository\Contracts\ILotRepository;
use App\Repository\Contracts\IMoneyRepository;
use App\Repository\Contracts\ITradeRepository;
use App\Repository\Contracts\IUserRepository;
use App\Repository\Contracts\IWalletRepository;
use App\Repository\CurrencyRepository;
use App\Repository\LotRepository;
use App\Repository\TradeRepository;
use App\Repository\UserRepository;
use App\Repository\WalletRepository;
use App\Repositorys\MoneyRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(IUserRepository::class,UserRepository::class);
        $this->app->singleton(ICurrencyRepository::class, CurrencyRepository::class);
        $this->app->singleton(ITradeRepository::class, TradeRepository::class);
        $this->app->singleton(ILotRepository::class,LotRepository::class);
        $this->app->singleton(IWalletRepository::class, WalletRepository::class);
        $this->app->singleton(IMoneyRepository::class, MoneyRepository::class);
    }
}
