<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class StockIssuance extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'issuance_slip_no',
        'staff_id',
        'issued_by',
        'approved_by',
        'issuance_date',
        'status',
        'handover_signature_path',
        'remarks'
    ];

    protected $casts = [
        'issuance_date' => 'date'
    ];

    public function staff()
    {
        return $this->belongsTo(Staff::class);
    }

    public function issuer()
    {
        return $this->belongsTo(User::class, 'issued_by');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function items()
    {
        return $this->hasMany(StockIssuanceItem::class);
    }
}
