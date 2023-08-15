<?php

namespace App\Contracts;

use App\DataTransferObjects\ProductDto;
use App\Models\Product;

interface ProductRepositoryInterface
{
    public function getAllProductsWithPaginate(ProductDto $arguments, $perPage = 15, $orderBy = '');
    public function create(ProductDto $arguments);
    public function update(Product $product, ProductDto $arguments);
    public function show(Product $product);
    public function delete(Product $product);
}
