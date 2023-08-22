<?php

namespace App\DataTransferObjects;

use Illuminate\Http\Request;

class UserDto
{
    public function __construct(
        public readonly ?string $firstName,
        public readonly ?string $lastName,
        public readonly ?string $mobile,
        public readonly ?string $password,
        public readonly ?string $mobileVerifiedAt,
        public readonly ?string $otp,
    ) {}

    public static function fromRequest(Request $request)
    {
        return new self(
            $request->validated('firstname'),
            $request->validated('lastname'),
            $request->validated('mobile'),
            $request->validated('password'),
            $request->validated('mobileVerifiedAt'),
            $request->validated('code'),
        );
    }

    public static function fromArray(array $array)
    {
        return new self(
            $array['firstname'] ?? null,
            $array['lastname'] ?? null,
            $array['mobile'] ?? null,
            $array['password'] ?? null,
            $array['mobileVerifiedAt'] ?? null,
            $array['code'] ?? null,
        );
    }
}
