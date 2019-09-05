<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    protected $fillable = [
        'name',
        'email',
        'price',
        'quantity',
        'is_vip',
        'product_id'
    ];

    public function products() {
        return $this->belongsTo(Ticket::class);
    }

    public function qr_codes() {
        return $this->hasOne(QRCode::class);
    }
}
