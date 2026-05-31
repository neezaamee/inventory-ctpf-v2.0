<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class StockIssuanceItem extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'stock_issuance_id',
        'batch_id',
        'quantity',
        'returned_quantity',
        'status'
    ];

    protected $casts = [
        'quantity' => 'integer',
        'returned_quantity' => 'integer'
    ];

    public function issuance()
    {
        return $this->belongsTo(StockIssuance::class, 'stock_issuance_id');
    }

    public function batch()
    {
        return $this->belongsTo(InventoryBatch::class, 'batch_id');
    }

    public function getActiveQuantityAttribute()
    {
        return $this->quantity - $this->returned_quantity;
    }
}
