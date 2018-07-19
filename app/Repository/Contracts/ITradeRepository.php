<?php

namespace App\Repository\Contracts;

use App\Entity\Trade;

interface ITradeRepository
{
    public function add(Trade $trade) : Trade;
}