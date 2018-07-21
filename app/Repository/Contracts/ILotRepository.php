<?php

namespace App\Repository\Contracts;

use App\Entity\Lot;

interface ILotRepository
{
    public function add(Lot $lot) : Lot;

    public function getByIdActive(int $id) : ?Lot;

    public function getById(int $id) : ?Lot;

    /**
     * @return Lot[]
     */
    public function findAll();

    public function findActiveLot(int $userId) : ?Lot;

    public function findActiveLotByUserAndCurrency(int $userId, int $currencyId): ?Lot;
}