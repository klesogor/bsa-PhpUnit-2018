<?php

namespace App\Repository\Contracts;

use App\Entity\Currency;

interface ICurrencyRepository
{
    public function add(Currency $currency) : Currency;

    public function getById(int $id) : ?Currency;

    public function getCurrencyByName(string $name) : ?Currency;

    /**
     * @return Currency[]
     */
    public function findAll();
}
