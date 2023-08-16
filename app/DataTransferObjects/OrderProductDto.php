<?php

namespace App\DataTransferObjects;

use Illuminate\Http\Request;

class OrderProductDto
{
    public function __construct(
        public readonly ?string $id,
        public readonly ?string $productId,
        public readonly ?string $orderId,
        public readonly ?string $count
    ) {}

    public static function fromRequest(Request $request)
    {
        return new self(
            $request->validated('id') ?? null,
            $request->validated('productId') ?? null,
            $request->validated('orderId') ?? null,
            $request->validated('count') ?? null,
        );
    }

    public static function fromArray(array $array)
    {
        return new self(
            $array['id'] ?? null,
            $array['productId'] ?? null,
            $array['orderId'] ?? null,
            $array['count'] ?? null,
        );
    }
}
