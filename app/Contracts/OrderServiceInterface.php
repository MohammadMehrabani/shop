<?php

namespace App\Contracts;

use App\DataTransferObjects\OrderDto;
use App\DataTransferObjects\OrderProductDto;
use App\Models\Order;

interface OrderServiceInterface
{
    public function getAllOrdersWithPaginate(OrderDto $orderDto, $perPage = 15, $orderBy = '');

    /**
     * @param OrderDto $orderDto
     * @param OrderProductDto[] $orderProducts
     * @param int|string $userId
     * @return mixed
     */
    public function store(OrderDto $orderDto, array $orderProducts, int|string $userId);
    public function show(Order $order);
}
