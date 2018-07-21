<?php


namespace App\Request;


use App\Request\Contracts\IMoneyRequest;

class MoneyRequest implements IMoneyRequest
{

    private $amount;
    private $walletId;
    private $currencyId;

    public function __construct(int $walletId,int $currencyId, float $amount)
    {
        $this->amount = $amount;
        $this->walletId = $walletId;
        $this->currencyId = $currencyId;
    }

    public function getWalletId(): int
    {
        return $this->walletId;
    }

    public function getCurrencyId(): int
    {
        return $this->currencyId;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }
}