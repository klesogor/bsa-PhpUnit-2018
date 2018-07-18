<?php

namespace App\Service;


use App\Entity\Money;
use App\Entity\Wallet;

use App\Exceptions\MarketException\BuyNegativeAmountException;
use App\Exceptions\MarketException\IncorrectMoneyAmountException;
use App\Exceptions\MarketException\WalletDoesntExistsException;
use App\Exceptions\MarketException\WalletDosentHaveEnoughMoneyException;
use App\Repository\Contracts\IMoneyRepository;
use App\Repository\Contracts\IWalletRepository;
use App\Request\Contracts\ICreateWalletRequest;
use App\Request\Contracts\IMoneyRequest;
use App\Service\Contracts\IWalletService;
use App\Service\Validators\Contracts\IWalletValidator;

class WalletService implements IWalletService
{

    private $moneyRepository;
    private $walletRepository;
    private $validator;

    public function __construct(IWalletRepository $walletRepository,
                                IMoneyRepository $moneyRepository,
                                IWalletValidator $validator)
    {
        $this->walletRepository = $walletRepository;
        $this->moneyRepository =  $moneyRepository;
        $this->validator = $validator;
    }

    /**
     * Add wallet to user.
     *
     * @param ICreateWalletRequest $walletRequest
     * @return Wallet
     *
     * @throws WalletDoesntExistsException
     */
    public function addWallet(ICreateWalletRequest $walletRequest): Wallet
    {
        $this->validator->validateCreateWalletRequest($walletRequest);
        $wallet = new Wallet();
        $wallet->user_id = $walletRequest->getUserId();
        $this->walletRepository->add($wallet);
    }

    /**
     * Add money to a wallet.
     * @param IMoneyRequest $moneyRequest
     * @return Money
     *
     * @throws IncorrectMoneyAmountException
     */
    public function addMoney(IMoneyRequest $moneyRequest): Money
    {
        $money = $this->moneyRepository
            ->findByWalletAndCurrency($moneyRequest->getWalletId(), $moneyRequest->getCurrencyId());
        if(is_null($money)){
            $money = new Money();
            $money->wallet_id = $moneyRequest->getWalletId();
            $money->currency_id = $moneyRequest->getCurrencyId();
        }

        $this->validator->validateAddMoney($moneyRequest,$money);

        $money->amount = $moneyRequest->getAmount();

        return $this->moneyRepository->save($money);
    }

    /**
     * Take money from a wallet.
     *
     * @param IMoneyRequest $moneyRequest
     * @return Money
     *
     * @throws BuyNegativeAmountException
     * @throws WalletDoesntExistsException
     * @throws WalletDosentHaveEnoughMoneyException
     */
    public function takeMoney(IMoneyRequest $moneyRequest): Money
    {
        $money = $this->moneyRepository
            ->findByWalletAndCurrency($moneyRequest->getWalletId(), $moneyRequest->getCurrencyId());

        $this->validator->validateTakeMoneyRequest($moneyRequest,$money);

        $money->amount = $money->money - $moneyRequest->getAmount();
        return $this->moneyRepository->save($money);
    }
}