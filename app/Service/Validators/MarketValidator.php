<?php

namespace App\Service\Validators;


use App\Entity\Lot;
use App\Exceptions\MarketException\BuyInactiveLotException;
use App\Exceptions\MarketException\BuyOwnCurrencyException;
use App\Exceptions\MarketException\IncorrectPriceException;
use App\Exceptions\MarketException\IncorrectTimeCloseException;
use App\Repository\Contracts\ILotRepository;
use App\Request\Contracts\IAddLotRequest;
use App\Request\Contracts\IBuyLotRequest;
use App\Service\Validators\Contracts\IMarketValidator;

class MarketValidator implements IMarketValidator
{
    private $lotRepository;

    public function __construct(ILotRepository $lotRepository)
    {
        $this->lotRepository = $lotRepository;
    }

    public function validateAddLot(IAddLotRequest $request): void
    {
        if($request->getDateTimeOpen() >= $request->getDateTimeClose()){
            throw new IncorrectTimeCloseException();
        }
        if($request->getPrice()<0){
            throw new IncorrectPriceException();
        }
    }

    public function validateBuyLot(IBuyLotRequest $request, ?Lot $lot): void
    {
        if($request->getUserId() === $lot->seller_id) {
            throw new BuyOwnCurrencyException();
        }
        if(is_null($lot)){
            throw new BuyInactiveLotException();
        }
    }

    public function validateGetLot(?Lot $lot): void
    {
        if(is_null($lot)){
            throw new BuyInactiveLotException();
        }
    }
}