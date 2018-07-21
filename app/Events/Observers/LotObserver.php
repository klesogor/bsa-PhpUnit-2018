<?php

namespace App\Events\Observers;

use App\Entity\Lot;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class LotObserver
{
    //convert timestamps

    public function retrieved(Lot $lot)
    {
        if (!is_int($lot->date_time_open)) {
           $lot->date_time_open = (new Carbon($lot->date_time_open))->getTimestamp();
        }
        if (!is_int($lot->date_time_close)) {
            $lot->date_time_close = (new Carbon($lot->date_time_close))->getTimestamp();
        }
    }

    public function saving(Lot $lot)
    {
        if (is_int($lot->date_time_open)) {
            $lot->date_time_open = Carbon::createFromTimestamp($lot->date_time_open)->format('Y-m-d H:i:s');
        }
        if (is_int($lot->date_time_close)) {
            $lot->date_time_close = Carbon::createFromTimestamp($lot->date_time_close)->format('Y-m-d H:i:s');
        }
    }
}