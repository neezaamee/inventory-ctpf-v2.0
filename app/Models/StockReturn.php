<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class StockReturn extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'return_slip_no',
        'staff_id',
        'received_by',
        'return_date',
        'reason',
        'action_taken',
        'remarks'
    ];

    protected $casts = [
        'return_date' => 'date'
    ];

    public function staff()
    {
        return $this->belongsTo(Staff::class);
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'received_by');
    }
}
