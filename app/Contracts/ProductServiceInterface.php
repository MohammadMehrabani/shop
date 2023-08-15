<?php

namespace App\Contracts;

use App\DataTransferObjects\ProductDto;
use App\Models\Product;

interface ProductServiceInterface
{
    public function getAllProductsWithPaginate(ProductDto $productDto, $perPage = 15, $orderBy = '');
    public function store(ProductDto $productDto);
    public function update(Product $product, ProductDto $productDto);
    public function show(Product $product);
    public function delete(Product $product);
}
