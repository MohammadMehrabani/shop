<?php

namespace App\Http\Controllers;

use App\Contracts\ProductServiceInterface;
use App\DataTransferObjects\ProductDto;
use App\Http\Requests\Product\IndexRequest;
use App\Http\Resources\ProductCollection;
use App\Traits\SharedControllers;

class ProductController extends Controller
{
    use SharedControllers;

    public function __construct(
        private ProductServiceInterface $productService
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(IndexRequest $request)
    {
        $dto = ProductDto::fromRequest($request);

        $data = new ProductCollection(
            $this->productService->getAllProductsWithPaginate($dto, $this->perPage(), $this->orderBy())
        );

        return response()->success($data);
    }
}
