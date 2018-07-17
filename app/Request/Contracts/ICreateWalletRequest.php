<?php

namespace App\Request\Contracts;

interface ICreateWalletRequest
{
    public function getUserId() : int;
}
