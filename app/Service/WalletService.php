<?php

namespace App\Service;


use App\Entity\Money;
use App\Entity\Wallet;
use App\Exceptions\WalletException\NegativeMoneyAddAmount;
use App\Exceptions\WalletException\PositiveMoneyTakeAmount;
use App\Exceptions\WalletException\WalletAlreadyExistsException;
use App\Repository\Contracts\IMoneyRepository;
use App\Repository\Contracts\IWalletRepository;
use App\Request\Contracts\ICreateWalletRequest;
use App\Request\Contracts\IMoneyRequest;
use App\Service\Contracts\IWalletService;

class WalletService implements IWalletService
{

    private $moneyRepository;
    private $walletRepository;

    public function __construct(IWalletRepository $walletRepository, IMoneyRepository $moneyRepository)
    {
        $this->walletRepository = $walletRepository;
        $this->moneyRepository =  $moneyRepository;
    }

    /**
     * Add wallet to user.
     *
     * @param ICreateWalletRequest $walletRequest
     * @return Wallet
     *
     * @throws WalletAlreadyExistsException
     */
    public function addWallet(ICreateWalletRequest $walletRequest): Wallet
    {
        if($this->walletRepository->findByUser($walletRequest->getUserId())){
            throw new WalletAlreadyExistsException();
        }

        $wallet = new Wallet();
        $wallet->user_id = $walletRequest->getUserId();
        $this->walletRepository->add($wallet);
    }

    /**
     * Add money to a wallet.
     * @param IMoneyRequest $moneyRequest
     * @return Money
     *
     * @throws NegativeMoneyAddAmount
     */
    public function addMoney(IMoneyRequest $moneyRequest): Money
    {
        if($moneyRequest->getAmount() < 0){
            throw new NegativeMoneyAddAmount();
        }
        $money = new Money();
        $money->wallet_id = $moneyRequest->getWalletId();
        $money->currency_id = $moneyRequest->getCurrencyId();
        $money->amount = $moneyRequest->getAmount();

        return $this->moneyRepository->save($money);
    }

    /**
     * Take money from a wallet.
     *
     * @param IMoneyRequest $moneyRequest
     * @return Money
     *
     * @throws PositiveMoneyTakeAmount
     */
    public function takeMoney(IMoneyRequest $moneyRequest): Money
    {
        if($moneyRequest->getAmount() > 0){
            throw new PositiveMoneyTakeAmount();
        }
        $money = new Money();
        $money->wallet_id = $moneyRequest->getWalletId();
        $money->currency_id = $moneyRequest->getCurrencyId();
        $money->amount = $moneyRequest->getAmount();

        return $this->moneyRepository->save($money);
    }
}