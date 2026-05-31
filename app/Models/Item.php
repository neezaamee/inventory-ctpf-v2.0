<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Item extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'category_id',
        'name',
        'sku',
        'size_attribute',
        'description',
        'min_quantity',
        'unit_of_measure',
        'is_trackable'
    ];

    protected $casts = [
        'is_trackable' => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function batches()
    {
        return $this->hasMany(InventoryBatch::class);
    }

    public function getAvailableStockAttribute()
    {
        return $this->batches()->sum('current_quantity');
    }

    public function getNeedsRestockAttribute()
    {
        return $this->available_stock <= $this->min_quantity;
    }
}
