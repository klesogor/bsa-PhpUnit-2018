<?php

namespace App\Repository\Contracts;

use App\User;

interface IUserRepository
{
    public function getById(int $id) : ?User;
}
