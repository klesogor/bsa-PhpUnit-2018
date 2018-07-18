<?php

namespace App\Service\Validators;


use App\Entity\Money;
use App\Exceptions\MarketException\BuyNegativeAmountException;
use App\Exceptions\MarketException\IncorrectLotAmountException;
use App\Exceptions\MarketException\IncorrectMoneyAmountException;
use App\Exceptions\MarketException\WalletAlreadyExistsException;
use App\Exceptions\MarketException\WalletDoesntExistsException;
use App\Exceptions\MarketException\WalletDosentHaveEnoughMoneyException;
use App\Repository\Contracts\IMoneyRepository;
use App\Repository\Contracts\IWalletRepository;
use App\Request\Contracts\ICreateWalletRequest;
use App\Request\Contracts\IMoneyRequest;
use App\Service\Validators\Contracts\IWalletValidator;

class WalletValidator implements IWalletValidator
{
    private $moneyRepository;
    private $walletRepository;

    public function __construct(IMoneyRepository $moneyRepository, IWalletRepository $walletRepository)
    {
        $this->moneyRepository = $moneyRepository;
        $this->walletRepository = $walletRepository;
    }

    public function validateTakeMoneyRequest(IMoneyRequest $request, ?Money $money): void
    {
        if($request->getAmount() < 1.0){
            throw new BuyNegativeAmountException();
        }

        if(is_null($money)){
            throw new WalletDoesntExistsException();
        }
        if($money->amount < $request->getAmount()){
            throw new IncorrectLotAmountException();
        }
    }

    public function validateAddMoney(IMoneyRequest $request, ?Money $money): void
    {
        if($request->getAmount() < 0){
            throw new IncorrectMoneyAmountException();
        }
    }

    public function validateCreateWalletRequest(ICreateWalletRequest $request) {
        if($this->walletRepository->findByUser($request->getUserId())){
            throw new WalletAlreadyExistsException();
        }
    }
}