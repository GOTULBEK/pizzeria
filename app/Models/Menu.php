<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use MongoDb\Laravel\Eloquent\Model;

class Menu extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'menu';

    protected $fillable = [
        'name', 'description', 'price', 'quantity', 'image'
    ];
}
