<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class Car extends Model
{
    /** @use HasFactory<\Database\Factories\CarFactory> */
    use HasFactory;
    use Searchable;

    protected $fillable = [
        'brand',
        'model',
        'year',
        'price_per_day',
        'is_available',
    ];
    public function rentals() {
        return $this->hasMany(Rental::class);
    }
    public function toSearchableArray(){
        return [
            'brand' => $this->brand,
            'model' => $this->model,
        ];
    }
}
