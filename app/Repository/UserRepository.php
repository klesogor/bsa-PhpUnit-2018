<?php

namespace App\Repository;


use App\Repository\Contracts\IUserRepository;
use App\User;

class UserRepository implements IUserRepository
{

    public function getById(int $id): ?User
    {
        return User::find($id);
    }
}