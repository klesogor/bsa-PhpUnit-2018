<?php

namespace App\Service\Contracts;

use App\Entity\Currency;
use App\Request\Contracts\IAddCurrencyRequest;

interface ICurrencyService
{
    public function addCurrency(IAddCurrencyRequest $currencyRequest) : Currency;
}
