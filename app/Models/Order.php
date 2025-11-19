<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use  SoftDeletes;

    public function order_details()
    {
        return $this->hasMany(OrderDetail::class);
    }

    public function table()
    {
        return $this->belongsTo(Table::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'updated_by_id', 'id');
    }
}
