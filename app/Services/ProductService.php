<?php

namespace App\Services;

use App\Contracts\ProductRepositoryInterface;
use App\Contracts\ProductServiceInterface;
use App\DataTransferObjects\ProductDto;
use App\Models\Product;

class ProductService implements ProductServiceInterface
{
    public function __construct(
        private ProductRepositoryInterface $productRepository,
    ) {}

    public function getAllProductsWithPaginate(ProductDto $productDto, $perPage = 15, $orderBy = '')
    {
        return $this->productRepository->getAllProductsWithPaginate($productDto, $perPage, $orderBy);
    }

    public function store(ProductDto $productDto)
    {
        return $this->productRepository->create($productDto);
    }

    public function update(Product $product, ProductDto $productDto)
    {
        return $this->productRepository->update($product, $productDto);
    }

    public function show(Product $product)
    {
        return $this->productRepository->show($product);
    }

    public function delete(Product $product)
    {
        return $this->productRepository->delete($product);
    }
}
