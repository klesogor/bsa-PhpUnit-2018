<?php

namespace App\Service\Contracts;


use App\Entity\Trade;
use App\User;

interface INotificationService
{
    public function notifyTradeCreated(User $recipient,User $buyer,Trade $trade):void;
}