<?php

namespace App\Contracts;

use App\DataTransferObjects\UserDto;

interface UserAuthenticateServiceInterface
{
    public function authenticate(UserDto $userDto);
    public function sendOtp(UserDto $userDto);
    public function verifyOtp(UserDto $userDto);
    public function register(UserDto $userDto);
    public function login(UserDto $userDto);
    public function refresh();
}
