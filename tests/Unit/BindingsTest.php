<?php

namespace Tests\Unit;


use App\Repository\Contracts\ICurrencyRepository;
use App\Repository\Contracts\ILotRepository;
use App\Repository\Contracts\IMoneyRepository;
use App\Repository\Contracts\ITradeRepository;
use App\Repository\Contracts\IUserRepository;
use App\Repository\Contracts\IWalletRepository;
use App\Service\Contracts\ICurrencyService;
use App\Service\Contracts\IMarketService;
use App\Service\Contracts\IWalletService;
use App\Service\Validators\Contracts\IMarketValidator;
use App\Service\Validators\Contracts\IWalletValidator;
use App\Service\Validators\MarketValidator;
use App\Service\Validators\WalletValidator;
use Tests\TestCase;

class BindingsTest extends TestCase
{
    public function test_bindings_repos()
    {
        $this->assertInstanceOf(
            ICurrencyRepository::class,
            $this->app->make(ICurrencyRepository::class)
            );
        $this->assertInstanceOf(
            ILotRepository::class,
            $this->app->make(ILotRepository::class)
        );
        $this->assertInstanceOf(
            IMoneyRepository::class,
            $this->app->make(IMoneyRepository::class)
        );
        $this->assertInstanceOf(
            ITradeRepository::class,
            $this->app->make(ITradeRepository::class)
        );
        $this->assertInstanceOf(
            IUserRepository::class,
            $this->app->make(IUserRepository::class)
        );
        $this->assertInstanceOf(
            IWalletRepository::class,
            $this->app->make(IWalletRepository::class)
        );
    }

    public function test_bindings_services()
    {
        $this->assertInstanceOf(
            ICurrencyService::class,
            $this->app->make(ICurrencyService::class)
        );
        $this->assertInstanceOf(
            IWalletService::class,
            $this->app->make(IWalletService::class)
        );
        $this->assertInstanceOf(
            IMarketService::class,
            $this->app->make(IMarketService::class)
        );
    }

    public function test_bindings_validators()
    {
        $this->assertInstanceOf(
            IWalletValidator::class,
            $this->app->make(WalletValidator::class)
        );

        $this->assertInstanceOf(
            IMarketValidator::class,
            $this->app->make(MarketValidator::class)
        );
    }
}