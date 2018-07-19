<?php

namespace App\Service;


use App\Entity\Trade;
use App\Mail\TradeCreated;
use App\Service\Contracts\INotificationService;
use App\User;
use Illuminate\Support\Facades\Mail;

class NotificationService implements INotificationService
{

    public function notifyTradeCreated(User $recipient, User $buyer, Trade $trade): void
    {
        $mail = new TradeCreated($trade,$recipient,$buyer);
        $mail->onQueue('notifications');
        Mail::queue($mail);
    }
}