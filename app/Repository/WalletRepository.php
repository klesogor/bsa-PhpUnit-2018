<?php

namespace App\Repository;


use App\Entity\Wallet;
use App\Repository\Contracts\IWalletRepository;


class WalletRepository implements IWalletRepository
{

    public function add(Wallet $wallet): Wallet
    {
        $wallet->save();
        return $wallet;
    }

    public function findByUser(int $userId): ?Wallet
    {
        return Wallet::where('user_id',$userId)->first();
    }
}