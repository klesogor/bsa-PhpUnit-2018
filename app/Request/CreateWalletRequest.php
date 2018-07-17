<?php

namespace App\Request;


use App\Request\Contracts\ICreateWalletRequest;

class CreateWalletRequest implements ICreateWalletRequest
{
    private $userId;

    public function __construct(int $userId)
    {
        $this->userId = $userId;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }
}