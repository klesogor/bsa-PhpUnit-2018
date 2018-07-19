<?php

namespace App\Service\Contracts;

use App\Entity\Money;
use App\Entity\Wallet;
use App\Request\Contracts\ICreateWalletRequest;
use App\Request\Contracts\IMoneyRequest;

interface IWalletService
{
    /**
     * Add wallet to user.
     *
     * @param ICreateWalletRequest $walletRequest
     * @return Wallet
     */
    public function addWallet(ICreateWalletRequest $walletRequest) : Wallet;

    /**
     * Add money to a wallet.
     * @param IMoneyRequest $moneyRequest
     *
     * @return Money
     */
    public function addMoney(IMoneyRequest $moneyRequest) : Money;

    /**
     * Take money from a wallet.
     *
     * @param IMoneyRequest $moneyRequest
     * @return Money
     */
    public function takeMoney(IMoneyRequest $moneyRequest) : Money;
}