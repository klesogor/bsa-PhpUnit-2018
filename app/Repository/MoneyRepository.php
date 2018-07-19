<?php

namespace App\Repositorys;


use App\Entity\Money;
use App\Repository\Contracts\IMoneyRepository;

class MoneyRepository implements IMoneyRepository
{

    public function save(Money $money): Money
    {
        $money->save();
        return $money;
    }

    public function findByWalletAndCurrency(int $walletId, int $currencyId): ?Money
    {
        return Money::where(['wallet_id' => $walletId,
            'currency_id' => $currencyId])
            ->first();
    }
}