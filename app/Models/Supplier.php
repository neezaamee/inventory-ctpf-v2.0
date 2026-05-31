<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Supplier extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'company_name',
        'contact_person',
        'phone',
        'email',
        'address',
        'ntn_number',
        'status'
    ];

    public function batches()
    {
        return $this->hasMany(InventoryBatch::class);
    }
}
