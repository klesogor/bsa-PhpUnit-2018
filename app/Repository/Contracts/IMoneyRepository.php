<?php

namespace App\Repository\Contracts;

use App\Entity\Money;

interface IMoneyRepository
{
    public function save(Money $money) : Money;

    public function findByWalletAndCurrency(int $walletId, int $currencyId) : ?Money;
}
