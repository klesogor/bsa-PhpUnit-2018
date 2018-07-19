<?php

namespace App\Response;


use App\Response\Contracts\ILotResponse;

class LotResponse implements ILotResponse
{
    private $id;
    private $userName;
    private $currencyName;
    private $amount;
    private $timeOpen;
    private $timeClose;
    private $price;

    public function __construct(
        int $id,
        string $userName,
        string $currencyName,
        float $amount,
        string $timeOpen,
        string  $timeClose,
        string $price)
    {
        $this->id = $id;
        $this->userName = $userName;
        $this->currencyName = $currencyName;
        $this->amount = $amount;
        $this->timeOpen = $timeOpen;
        $this->timeClose = $timeClose;
        $this->price = $price;
    }

    /**
     * An identifier of lot
     *
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    public function getUserName(): string
    {
        return $this->userName;
    }

    public function getCurrencyName(): string
    {
        return $this->currencyName;
    }

    /**
     * All amount of currency that user has in the wallet.
     *
     * @return float
     */
    public function getAmount(): float
    {
        return $this->amount;
    }

    /**
     * Format: yyyy/mm/dd hh:mm:ss
     *
     * @return string
     */
    public function getDateTimeOpen(): string
    {
        return $this->timeOpen;
    }

    /**
     * Format: yyyy/mm/dd hh:mm:ss
     *
     * @return string
     */
    public function getDateTimeClose(): string
    {
       return $this->timeClose;
    }

    /**
     * Price per one amount of currency.
     *
     * Format: 00,00
     *
     * @return string
     */
    public function getPrice(): string
    {
        return $this->price;
    }
}