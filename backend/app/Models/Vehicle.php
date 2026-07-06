<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Vehicle extends Model
{
    use HasFactory;

    protected $fillable = [
        'slug',
        'title',
        'lot',
        'vin',
        'year',
        'make',
        'model',
        'body',
        'engine',
        'transmission',
        'fuel',
        'color',
        'condition',
        'location',
        'auction',
        'sale_date',
        'odometer',
        'current_bid',
        'buy_now',
        'shipping_fee',
        'featured',
        'private_sale',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'title' => 'array',
            'sale_date' => 'datetime',
            'featured' => 'boolean',
            'private_sale' => 'boolean',
        ];
    }

    public function images(): HasMany
    {
        return $this->hasMany(VehicleImage::class)->orderBy('sort_order');
    }
}

