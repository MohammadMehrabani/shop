<?php

namespace App\Services;

use App\Contracts\AdminAuthenticateServiceInterface;
use App\DataTransferObjects\AdminDto;
use Illuminate\Auth\AuthenticationException;

class AdminAuthenticateService implements AdminAuthenticateServiceInterface
{
    public function login(AdminDto $adminDto)
    {
        $token = auth()->guard('admin')
                        ->attempt([
                            'email' => $adminDto->email,
                            'password' => $adminDto->password
                        ]);
        if (! $token) {
            throw new AuthenticationException();
        }

        return $this->respondWithToken($token);
    }

    private function respondWithToken($token)
    {
        return [
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ];
    }
}
