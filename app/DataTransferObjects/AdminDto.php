<?php

namespace App\DataTransferObjects;

use Illuminate\Http\Request;

class AdminDto
{
    public function __construct(
        public readonly ?string $name,
        public readonly ?string $email,
        public readonly ?string $password
    ) {}

    public static function fromRequest(Request $request)
    {
        return new self(
            $request->validated('name'),
            $request->validated('email'),
            $request->validated('password'),
        );
    }

    public static function fromArray(array $array)
    {
        return new self(
            $array['name'] ?? null,
            $array['email'] ?? null,
            $array['password'] ?? null,
        );
    }
}
