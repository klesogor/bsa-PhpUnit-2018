<?php

namespace App\Request\Contracts;

interface IBuyLotRequest
{
    public function getUserId() : int;

    public function getLotId() : int;

    public function getAmount() : float;
}