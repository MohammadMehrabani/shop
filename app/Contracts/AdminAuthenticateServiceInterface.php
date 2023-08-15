<?php

namespace App\Contracts;

use App\DataTransferObjects\AdminDto;

interface AdminAuthenticateServiceInterface
{
    public function login(AdminDto $adminDto);
}
