<?php

namespace App\Providers;

use App\Request\AddCurrencyRequest;
use App\Request\AddLotRequest;
use App\Request\BuyLotRequest;
use App\Request\Contracts\IAddCurrencyRequest;
use App\Request\Contracts\IAddLotRequest;
use App\Request\Contracts\IBuyLotRequest;
use App\Request\Contracts\ICreateWalletRequest;
use App\Request\Contracts\IMoneyRequest;
use App\Request\CreateWalletRequest;
use App\Request\MoneyRequest;
use Illuminate\Support\ServiceProvider;

class RequestProvider extends ServiceProvider
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
        $this->app->bind(IAddCurrencyRequest::class, AddCurrencyRequest::class);
        $this->app->bind(IAddLotRequest::class, AddLotRequest::class);
        $this->app->bind(   IBuyLotRequest::class, BuyLotRequest::class);
        $this->app->bind(ICreateWalletRequest::class, CreateWalletRequest::class);
        $this->app->bind(IMoneyRequest::class, MoneyRequest::class);
    }
}
