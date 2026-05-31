<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class StockReceive extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'grn_number',
        'supplier_id',
        'received_by',
        'received_date',
        'invoice_number',
        'total_amount',
        'remarks'
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'received_date' => 'date'
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'received_by');
    }
}
