<?php

namespace App\Entity;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Lot extends Model
{
    protected $fillable = [
        "id",
        'currency_id',
        'seller_id',
        'date_time_open',
        'date_time_close',
        'price'
    ];

    public function getDateTimeOpen() : int
    {
        if (is_int($this->date_time_open)) {
            return $this->date_time_open;
        } else {
            return (new Carbon($this->date_time_open))->getTimestamp();
        }
    }

    public function getDateTimeClose() : int
    {
        if (is_int($this->date_time_close)) {
            return $this->date_time_close;
        } else {
            return (new Carbon($this->date_time_close))->getTimestamp();
        }
    }

    public function scopeActive($query)
    {
        return $query->where('date_time_close','>',Carbon::now()->format('Y-m-d h:i:s'));
    }

    public function owner()
    {
        return $this->belongsTo(\App\User::class,'seller_id');
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }
}
