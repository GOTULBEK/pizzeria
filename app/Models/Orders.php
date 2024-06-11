<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use MongoDb\Laravel\Eloquent\Model;

class Orders extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'orders';

    protected $fillable = [
        'user_id', 'menu_item_id', 'quantity', 'price_total', 'status', 'created_at', 'updated_at',
    ];
}
