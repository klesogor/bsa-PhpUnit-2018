<?php

namespace App\Mail;

use App\Entity\Trade;
use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class TradeCreated extends Mailable
{
    use Queueable, SerializesModels;

    private $trade;
    private $recipient;
    private $buyer;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Trade $trade,User $recipient,User $buyer)
    {
        $this->trade = $trade;
        $this->recipient = $recipient;
        $this->buyer = $buyer;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('mail.tradeCreated',[
            'buyerName'=> $this->buyer->name,
            'amount'=> $this->trade->amount,
            'lotId'=>$this->trade->lotId,
        ])->to($this->recipient->email);
    }
}
