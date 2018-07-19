<?php

namespace App\Request\Contracts;

interface IAddLotRequest
{
    public function getCurrencyId() : int;

    /**
     * An identifier of user
     *
     * @return int
     */
    public function getSellerId() : int;

    /**
     * Timestamp
     *
     * @return int
     */
    public function getDateTimeOpen() : int;

    /**
     * Timestamp
     *
     * @return int
     */
    public function getDateTimeClose() : int;

    public function getPrice() : float;
}