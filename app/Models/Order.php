<?php

namespace App\Models;

use App\DataTransferObjects\OrderDto;
use App\Traits\SharedModels;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory, SharedModels, SoftDeletes;

    protected $fillable = [
        'user_id',
        'total_amount'
    ];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
    ];

    protected $orderableColumns = [
        '_id',
        'total_amount',
    ];

    public function scopeFilter($query, OrderDto $arguments)
    {
        if ($arguments->userId)
            $query->where('user_id', $arguments->userId);
        if ($arguments->totalAmount)
            $query->where('total_amount', $arguments->totalAmount);

        return $query;
    }

    public function orderProducts()
    {
        return $this->hasMany(OrderProduct::class, 'order_id');
    }
}
