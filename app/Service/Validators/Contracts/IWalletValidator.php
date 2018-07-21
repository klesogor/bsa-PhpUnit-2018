<?php

namespace App\Service\Validators\Contracts;


use App\Entity\Money;
use App\Repository\Contracts\IMoneyRepository;
use App\Repository\Contracts\IWalletRepository;
use App\Request\Contracts\ICreateWalletRequest;
use App\Request\Contracts\IMoneyRequest;

interface IWalletValidator
{
    public function __construct(IMoneyRepository $repository,IWalletRepository $walletRepository);

    public function validateCreateWalletRequest(ICreateWalletRequest $request);

    public function validateTakeMoneyRequest(IMoneyRequest $request, ?Money $money):void;

    public function validateAddMoney(IMoneyRequest $request, ?Money $money):void;
}