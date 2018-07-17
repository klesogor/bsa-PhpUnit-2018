<?php

namespace App\Request\Contracts;

interface IAddCurrencyRequest
{
    public function getName() : string;
}