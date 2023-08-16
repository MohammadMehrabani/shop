<?php

namespace App\Models;

use App\DataTransferObjects\OrderProductDto;
use App\Traits\SharedModels;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\SoftDeletes;

class OrderProduct extends Model
{
    use HasFactory, SharedModels, SoftDeletes;

    protected $fillable = [
        'order_id',
        'product_id',
        'count',
    ];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
    ];

    protected $orderableColumns = [
        '_id',
        'order_id',
    ];

    public function scopeFilter($query, OrderProductDto $arguments)
    {
        if ($arguments->orderId)
            $query->where('order_id', $arguments->orderId);
        if ($arguments->productId)
            $query->where('product_id', $arguments->productId);

        return $query;
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
