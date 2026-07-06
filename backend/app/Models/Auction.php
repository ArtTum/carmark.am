<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Auction extends Model
{
    protected $fillable = ['name', 'address', 'country', 'starts_at', 'lots_count', 'buyer_fee', 'image', 'active'];

    protected function casts(): array
    {
        return [
            'starts_at' => 'datetime',
            'active' => 'boolean',
        ];
    }
}
