<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CreateBills
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    protected $contract;

    public function __construct(contracts $contract)
    {
        $this->contract = $contract;
    }

    public function handle()
    {
        // Tính tổng percent_amount của deposit_amounts trong hợp đồng
        $totalPercentAmount = $this->contracts->depositAmounts->sum('percent_amount');

        // Tạo một bản ghi bill
        $bill = new bills([
            'user_id' => $this->contracts->customer_id,
            'contract_id' => $this->contracts->id,
            'note' => 'Tổng percent_amount: ' . $totalPercentAmount,
        ]);

        $bill->save();
    }
}

