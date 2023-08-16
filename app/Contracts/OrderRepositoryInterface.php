<?php

namespace App\Contracts;

use App\DataTransferObjects\OrderDto;
use App\Models\Order;

interface OrderRepositoryInterface
{
    public function getAllOrdersWithPaginate(OrderDto $arguments, $perPage = 15, $orderBy = '');
    public function create(OrderDto $arguments);
    public function show(Order $order);
    public function update(Order $order, OrderDto $arguments);
}
