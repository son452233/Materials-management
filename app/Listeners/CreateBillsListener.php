<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class CreateBillsListener
{
    public function handle(CreateBills $event)
    {
        event(new BillGenerated($event->contracts));
    }
}

