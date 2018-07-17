<?php

namespace App\Service;


use App\Entity\Lot;
use App\Entity\Trade;
use App\Exceptions\MarketException\ActiveLotExistsException;
use App\Exceptions\MarketException\BuyInactiveLotException;
use App\Exceptions\MarketException\BuyNegativeAmountException;
use App\Exceptions\MarketException\BuyOwnCurrencyException;
use App\Exceptions\MarketException\IncorrectLotAmountException;
use App\Exceptions\MarketException\IncorrectPriceException;
use App\Exceptions\MarketException\IncorrectTimeCloseException;
use App\Exceptions\MarketException\LotDoesNotExistException;
use App\Repository\Contracts\ILotRepository;
use App\Repository\Contracts\IMoneyRepository;
use App\Repository\Contracts\IUserRepository;
use App\Repository\Contracts\IWalletRepository;
use App\Request\Contracts\IAddLotRequest;
use App\Request\Contracts\IBuyLotRequest;
use App\Response\Contracts\LotResponse;
use App\Service\Contracts\IMarketService;

class MarketService implements IMarketService
{

    private $userRepository;
    private $moneyRepository;
    private $lotRepository;
    private $walletRepository;

    public function __construct(IUserRepository $userRepo,
                                IMoneyRepository $moneyRepo,
                                ILotRepository $lotRepo,
                                IWalletRepository $walletRepo)
    {
        $this->userRepository = $userRepo;
        $this->moneyRepository = $moneyRepo;
        $this->lotRepository = $lotRepo;
        $this->walletRepository = $walletRepo;
    }


    /**
     * Sell currency.
     *
     * @param IAddLotRequest $lotRequest
     *
     * @throws ActiveLotExistsException
     * @throws IncorrectTimeCloseException
     * @throws IncorrectPriceException
     *
     * @return Lot
     */
    public function addLot(IAddLotRequest $lotRequest): Lot
    {
       
    }

    /**
     * Buy currency.
     *
     * @param IBuyLotRequest $lotRequest
     *
     * @throws BuyOwnCurrencyException
     * @throws IncorrectLotAmountException
     * @throws BuyNegativeAmountException
     * @throws BuyInactiveLotException
     *
     * @return Trade
     */
    public function buyLot(IBuyLotRequest $lotRequest): Trade
    {
        // TODO: Implement buyLot() method.
    }

    /**
     * Retrieves lot by an identifier and returns it in LotResponse format
     *
     * @param int $id
     *
     * @throws LotDoesNotExistException
     *
     * @return LotResponse
     */
    public function getLot(int $id): LotResponse
    {
        // TODO: Implement getLot() method.
    }

    /**
     * Return list of lots.
     *
     * @return LotResponse[]
     */
    public function getLotList(): array
    {
        // TODO: Implement getLotList() method.
    }
}