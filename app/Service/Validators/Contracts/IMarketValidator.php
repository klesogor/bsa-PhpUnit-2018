<?php


namespace App\Service\Validators\Contracts;


use App\Entity\Lot;
use App\Repository\Contracts\ILotRepository;
use App\Request\Contracts\IAddLotRequest;
use App\Request\Contracts\IBuyLotRequest;

interface IMarketValidator
{
    public function __construct(ILotRepository $lotRepository);

    public function validateAddLot(IAddLotRequest $request): void;

    public function validateBuyLot(IBuyLotRequest $request, ?Lot $lot): void;

    public function validateGetLot(?Lot $lot):void;
}