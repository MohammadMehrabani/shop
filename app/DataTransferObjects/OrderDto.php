<?php

namespace App\DataTransferObjects;

use Illuminate\Http\Request;

class OrderDto
{
    public function __construct(
        public readonly ?string $id,
        public readonly ?string $userId,
        public readonly ?string $totalAmount
    ) {}

    public static function fromRequest(Request $request)
    {
        return new self(
            $request->validated('id') ?? null,
            $request->validated('userId') ?? null,
            $request->validated('totalAmount') ?? null,
        );
    }

    public static function fromArray(array $array)
    {
        return new self(
            $array['id'] ?? null,
            $array['userId'] ?? null,
            $array['totalAmount'] ?? null,
        );
    }
}
