<?php

namespace App\Services;

use App\Contracts\OrderProductRepositoryInterface;
use App\Contracts\OrderRepositoryInterface;
use App\Contracts\OrderServiceInterface;
use App\Contracts\ProductRepositoryInterface;
use App\DataTransferObjects\OrderDto;
use App\DataTransferObjects\OrderProductDto;
use App\Exceptions\ApiException;
use App\Models\Order;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderService implements OrderServiceInterface
{
    public function __construct(
        private OrderRepositoryInterface $orderRepository,
        private OrderProductRepositoryInterface $orderProductRepository,
        private ProductRepositoryInterface $productRepository,
    ) {}

    public function getAllOrdersWithPaginate(OrderDto $orderDto, $perPage = 15, $orderBy = '')
    {
        return $this->orderRepository->getAllOrdersWithPaginate($orderDto, $perPage, $orderBy);
    }

    /**
     * @param OrderDto $orderDto
     * @param OrderProductDto[] $orderProducts
     * @return mixed
     */
    public function store(OrderDto $orderDto, array $orderProducts)
    {
        try {
            DB::beginTransaction();
            $_orderProducts = collect();
            $countPerProduct = [];
            foreach ($orderProducts as $orderProductDto) {
                $_orderProducts->push([
                    'product_id' => $orderProductDto->productId,
                    'count' => $orderProductDto->count,
                    'created_at' => now()->format('Y-m-d H:i:s'),
                    'updated_at' => now()->format('Y-m-d H:i:s'),
                ]);

                $countPerProduct[$orderProductDto->productId] = $orderProductDto->count;
            }

            $products = $this->checkOrderItemsExists($_orderProducts);

            $this->checkProductAvailable($products, $countPerProduct);

            $order = $this->orderRepository->create($orderDto);
            $_orderProducts = $_orderProducts->map(function ($item) use($order) {
                $item['order_id'] = $order->id;
                return $item;
            });

            $totalAmount = $this->productUpdateInventory($products, $countPerProduct);
            $this->orderProductRepository->insert($_orderProducts->toArray());
            $order = $this->orderRepository->update($order, OrderDto::fromArray(['totalAmount' => $totalAmount]));
            DB::commit();

            return $order;
        } catch (\Exception $e) {

            DB::rollBack();
            Log::error($e);

            if ($e instanceof ApiException) {
                throw new ApiException($e->getMessage(), $e->getCode());
            }
        }

        return false;
    }

    public function show(Order $order, $userId)
    {
        if ($order->user_id != $userId)
            throw new ApiException('access denied', 403);

        return $this->orderRepository->show($order);
    }

    private function productUpdateInventory($products, $countPerProduct)
    {
        $totalAmount = 0;

        foreach ($products as $product) {
            $totalAmount += $product->price * $countPerProduct[$product->id];

            $product = $this->productRepository->decrementInventory($product, $countPerProduct[$product->id]);

            if ($product->inventory < 0)
                throw new ApiException('the product '.$product->id.' is not available', 400);
        }

        return $totalAmount;
    }

    private function checkOrderItemsExists($_orderProducts)
    {
        $productsId = $_orderProducts->pluck('product_id')->toArray();
        $products = $this->productRepository->getProductsWithIds($productsId);
        $checkProducts = $products->pluck('_id')->toArray();
        if (count($checkProducts) != count($productsId)) {
            $productNotExists = array_diff($productsId, $checkProducts);

            if (!empty($productNotExists)) {
                throw new ApiException('product items '.implode(', ', $productNotExists).' not exists.', 400);
            }
        }

        return $products;
    }

    private function checkProductAvailable($products, $countPerProduct)
    {
        foreach ($products as $product) {

            if($product->inventory < $countPerProduct[$product->id])
                throw new ApiException('The product '.$product->id.' is not available', 400);

        }
    }
}
