<?php

namespace App\Repositories;

use App\Contracts\OrderRepositoryInterface;
use App\DataTransferObjects\OrderDto;
use App\Models\Order;

class MongoOrderRepository extends MongoBaseRepository implements OrderRepositoryInterface
{
    public function model()
    {
        return Order::class;
    }

    public function getAllOrdersWithPaginate(OrderDto $arguments, $perPage = 15, $orderBy = '')
    {
        $query = Order::query()->filter($arguments)->customOrderBy($orderBy);
        return $query->paginate($perPage);
    }

    public function create(OrderDto $arguments)
    {
        return Order::create([
            'user_id'       => $arguments->userId,
            'total_amount'  => $arguments->totalAmount
        ]);
    }

    public function show(Order $order)
    {
        return $order->load('orderProducts.product');
    }

    public function update(Order $order, OrderDto $arguments)
    {
        $order->update([
            'user_id'        => $arguments->userId ?: $order->user_id,
            'total_amount'   => $arguments->totalAmount ?: $order->total_amount,
        ]);

        return $order;
    }
}
