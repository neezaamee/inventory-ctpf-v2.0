<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class InventoryBatch extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'item_id',
        'supplier_id',
        'batch_number',
        'unit_purchase_cost',
        'initial_quantity',
        'current_quantity',
        'received_date',
        'expiry_date'
    ];

    protected $casts = [
        'unit_purchase_cost' => 'decimal:2',
        'initial_quantity' => 'integer',
        'current_quantity' => 'integer',
        'received_date' => 'date',
        'expiry_date' => 'date'
    ];

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }
}
