<?php

namespace App\Service\Contracts;

use App\Entity\{ Lot, Trade};
use App\Request\Contracts\{ IAddLotRequest, IBuyLotRequest };
use App\Response\Contracts\ILotResponse;
use App\Exceptions\MarketException\{
    ActiveLotExistsException,
    IncorrectPriceException,
    IncorrectTimeCloseException,
    BuyOwnCurrencyException,
    IncorrectLotAmountException,
    BuyNegativeAmountException,
    BuyInactiveLotException,
    LotDoesNotExistException
};

interface IMarketService
{
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
    public function addLot(IAddLotRequest $lotRequest) : Lot;

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
    public function buyLot(IBuyLotRequest $lotRequest) : Trade;

    /**
     * Retrieves lot by an identifier and returns it in LotResponse format
     *
     * @param int $id
     * 
     * @throws LotDoesNotExistException
     * 
     * @return ILotResponse
     */
    public function getLot(int $id) : ILotResponse;

    /**
     * Return list of lots.
     *
     * @return ILotResponse[]
     */
    public function getLotList() : array;
}
