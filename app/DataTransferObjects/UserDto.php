<?php

namespace App\DataTransferObjects;

use Illuminate\Http\Request;
use Illuminate\Support\ValidatedInput;

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

    public static function fromRequest(Request|ValidatedInput $request)
    {
        return new self(
            $request->firstname ?? null,
            $request->lastname ?? null,
            $request->mobile ?? null,
            $request->password ?? null,
            $request->mobileVerifiedAt ?? null,
            $request->code ?? null,
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
