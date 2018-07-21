<?php

namespace App\Presenters;

use App\Response\Contracts\ILotResponse;

class LotResponsePresenter
{
    public static function presentLotResponse(ILotResponse $response):array
    {
        return static::lotToArray($response);
    }

    /**
     * @param ILotResponse[] $responses
     * @return string
     */
    public static function presentLotResponseArray(array $responses):array
    {
        return array_map(function($lotResponse){
            return static::lotToArray($lotResponse);
        },$responses);
    }

    private static function lotToArray(ILotResponse $response): array
    {
        return [
            'id' => $response->getId(),
            'user_name' => $response->getUserName(),
            'currency_name' => $response->getCurrencyName(),
            'amount' => $response->getAmount(),
            'date_time_open' => $response->getDateTimeOpen(),
            'date_time_close' => $response->getDateTimeClose(),
            'price' => $response->getPrice(),
        ];
    }
}