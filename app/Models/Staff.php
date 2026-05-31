<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class Staff extends Model implements Auditable
{
    use HasFactory, SoftDeletes, \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'belt_no',
        'cnic',
        'first_name',
        'last_name',
        'rank',
        'gender',
        'phone_no',
        'current_posting',
        'status'
    ];

    public function issuances()
    {
        return $this->hasMany(StockIssuance::class);
    }

    public function returns()
    {
        return $this->hasMany(StockReturn::class);
    }

    public function user()
    {
        return $this->hasOne(User::class);
    }

    public function getFullNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function getDesignationAttribute()
    {
        return "{$this->rank} (Belt No. {$this->belt_no})";
    }
}
