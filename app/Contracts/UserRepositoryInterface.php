<?php

namespace App\Contracts;

use App\DataTransferObjects\UserDto;
use App\Models\User;

interface UserRepositoryInterface
{
    public function create(UserDto $arguments);
    public function update(User $user, UserDto $arguments);
    public function findByMobile($mobile);
}
