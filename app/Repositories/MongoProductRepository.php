<?php

namespace App\Repositories;

use App\Contracts\ProductRepositoryInterface;
use App\DataTransferObjects\ProductDto;
use App\Models\Product;

class MongoProductRepository extends MongoBaseRepository implements ProductRepositoryInterface
{
    public function model()
    {
        return Product::class;
    }

    public function getAllProductsWithPaginate(ProductDto $arguments, $perPage = 15, $orderBy = '')
    {
        $query = Product::query()->filter($arguments)->customOrderBy($orderBy);
        return $query->paginate($perPage);
    }

    public function create(ProductDto $arguments)
    {
        return Product::create([
            'title'     => $arguments->title,
            'price'     => $arguments->price,
            'inventory' => $arguments->inventory
        ]);
    }

    public function update(Product $product, ProductDto $arguments)
    {
        $product->update([
            'title'     => $arguments->title     ?: $product->title,
            'price'     => $arguments->price     ?: $product->price,
            'inventory' => $arguments->inventory ?: $product->inventory,
        ]);

        return $product;
    }

    public function show(Product $product)
    {
        return $product;
    }

    public function delete(Product $product)
    {
        return $product->delete();
    }
}
