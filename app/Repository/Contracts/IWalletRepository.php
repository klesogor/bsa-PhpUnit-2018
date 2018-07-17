<?php

namespace App\Repository\Contracts;

use App\Entity\Wallet;

interface IWalletRepository
{
    public function add(Wallet $wallet) : Wallet;

    public function findByUser(int $userId) : ?Wallet;
}