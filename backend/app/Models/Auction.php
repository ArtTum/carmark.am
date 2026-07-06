<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Auction extends Model
{
    protected $fillable = ['name', 'address', 'starts_in', 'cars_count', 'fee', 'image', 'active'];

    protected function casts(): array
    {
        return ['active' => 'boolean'];
    }
}

