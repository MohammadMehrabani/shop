<?php

namespace App\Http\Controllers\Admin;

use App\Contracts\ProductServiceInterface;
use App\DataTransferObjects\ProductDto;
use App\Http\Controllers\Controller;
use App\Http\Requests\Product\IndexRequest;
use App\Http\Requests\Product\StoreRequest;
use App\Http\Requests\Product\UpdateRequest;
use App\Http\Resources\ProductCollection;
use App\Models\Product;
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

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRequest $request)
    {
        $dto = ProductDto::fromRequest($request);

        $data = $this->productService->store($dto);

        if ($data)
            return response()->success($data);
        else
            return response()->error('not successfully create');
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        return response()->success($this->productService->show($product));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Product $product, UpdateRequest $request)
    {
        $dto = ProductDto::fromRequest($request);

        $data = $this->productService->update($product, $dto);

        if ($data)
            return response()->success($data);
        else
            return response()->error('not successfully update');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        $data = $this->productService->delete($product);

        if ($data)
            return response()->success('deleted successfully');
        else
            return response()->error('not successfully deleted');
    }
}
