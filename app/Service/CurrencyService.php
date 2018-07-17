<?php

namespace App\Service;


use App\Entity\Currency;
use App\Repository\Contracts\ICurrencyRepository;
use App\Request\Contracts\IAddCurrencyRequest;
use App\Service\Contracts\ICurrencyService;

class CurrencyService implements ICurrencyService
{

    public function addCurrency(IAddCurrencyRequest $currencyRequest): Currency
    {
        $currency = new Currency();
        $currency->name = $currencyRequest->getName();
        return app(ICurrencyRepository::class)->add($currency);
    }
}