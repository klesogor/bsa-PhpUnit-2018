<?php

namespace App\Repository;


use App\Entity\Trade;
use App\Repository\Contracts\ITradeRepository;

class TradeRepository implements ITradeRepository
{

    public function add(Trade $trade): Trade
    {
        $trade->save();
        return $trade;
    }
}