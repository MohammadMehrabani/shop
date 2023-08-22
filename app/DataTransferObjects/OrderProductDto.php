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
            $request->validated('id'),
            $request->validated('productId'),
            $request->validated('orderId'),
            $request->validated('count'),
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
