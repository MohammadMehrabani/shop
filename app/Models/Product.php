<?php

namespace App\Models;

use App\DataTransferObjects\ProductDto;
use App\Traits\SharedModels;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SharedModels, SoftDeletes;

    protected $fillable = [
        'title',
        'price',
        'inventory',
    ];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
    ];

    protected $orderableColumns = [
        '_id',
        'inventory',
    ];

    public function scopeFilter($query, ProductDto $arguments)
    {
        if ($arguments->title)
            $query->where('title', 'LIKE', '%' . $arguments->title . '%');

        return $query;
    }
}
