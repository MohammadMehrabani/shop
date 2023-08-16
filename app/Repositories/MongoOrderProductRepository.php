<?php

namespace App\Repositories;

use App\Contracts\OrderProductRepositoryInterface;
use App\Models\OrderProduct;

class MongoOrderProductRepository extends MongoBaseRepository implements OrderProductRepositoryInterface
{
    public function model()
    {
        return OrderProduct::class;
    }

    public function insert(array $data)
    {
        return OrderProduct::query()->insert($data);
    }
}
