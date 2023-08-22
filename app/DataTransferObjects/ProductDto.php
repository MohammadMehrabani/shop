<?php

namespace App\DataTransferObjects;

use Illuminate\Http\Request;

class ProductDto
{
    public function __construct(
        public readonly ?string $id,
        public readonly ?string $title,
        public readonly ?string $price,
        public readonly ?string $inventory
    ) {}

    public static function fromRequest(Request $request)
    {
        return new self(
            $request->validated('id'),
            $request->validated('title'),
            $request->validated('price'),
            $request->validated('inventory')
        );
    }

    public static function fromArray(array $array)
    {
        return new self(
            $array['id'] ?? null,
            $array['title'] ?? null,
            $array['price'] ?? null,
            $array['inventory'] ?? null
        );
    }
}
