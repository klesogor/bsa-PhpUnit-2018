<?php

namespace App\Repository;

use App\Entity\Lot;
use App\Repository\Contracts\ILotRepository;

class LotRepository implements  ILotRepository
{

    public function add(Lot $lot): Lot
    {
        $lot->save();
        return $lot;
    }

    public function getByIdActive(int $id): ?Lot//returns only active
    {
        return  Lot::active()
            ->where('id',$id)
            ->first();
    }

    public function getById(int $id): ?Lot
    {
        return Lot::find($id);
    }

    /**
     * @return Lot[]
     */
    public function findAll()
    {
        return Lot::all();
    }

    /**
     * @return Lot[]
     */
    public function findAllActive()
    {
        return Lot::active()->get();
    }

    public function findActiveLot(int $userId): ?Lot
    {
       return Lot::active()
           ->where('user_id',$userId)
           ->first();
    }
}