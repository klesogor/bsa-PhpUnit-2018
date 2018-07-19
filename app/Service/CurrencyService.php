<?php

namespace App\Service;


use App\Entity\Currency;
use App\Repository\Contracts\ICurrencyRepository;
use App\Request\Contracts\IAddCurrencyRequest;
use App\Service\Contracts\ICurrencyService;

class CurrencyService implements ICurrencyService
{
    private $repository;

    public function __construct(ICurrencyRepository $repository)
    {
        $this->repository = $repository;
    }

    public function addCurrency(IAddCurrencyRequest $currencyRequest): Currency
    {
        $currency = new Currency();
        $currency->name = $currencyRequest->getName();
        return $this->repository->add($currency);
    }
}