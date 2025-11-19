<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Table extends Model
{
    use SoftDeletes;

    public function order_detail_temps()
    {
        return $this->hasMany(OrderDetailTemp::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'updated_by_id', 'id');
    }
}
