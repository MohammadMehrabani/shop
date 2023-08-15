<?php

namespace App\Http\Controllers\Admin;

use App\Contracts\AdminAuthenticateServiceInterface;
use App\DataTransferObjects\AdminDto;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\LoginRequest;

class AuthController extends Controller
{
    public function __construct(
        private AdminAuthenticateServiceInterface $adminAuthenticateService
    ) {}

    public function login(LoginRequest $request) {

        $dto = AdminDto::fromRequest($request);

        return response()->success($this->adminAuthenticateService->login($dto));
    }

    public function me()
    {
        return response()->success(auth('admin')->user());
    }
}
