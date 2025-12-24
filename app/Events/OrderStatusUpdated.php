<?php

namespace App\Events;

use App\Models\Order;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderStatusUpdated implements ShouldBroadcast
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public Order $order,
        public string $previousStatus,
        public string $newStatus,
    ) {}

    public function broadcastOn(): array
    {
        $channels = [
            new PrivateChannel('orders.customer.' . $this->order->user_id),
        ];

        if ($this->order->vendor_id) {
            $channels[] = new PrivateChannel('orders.vendor.' . (int) $this->order->vendor_id);
        }

        return $channels;
    }

    public function broadcastAs(): string
    {
        return 'order.status-updated';
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->order->id,
            'status' => $this->newStatus,
            'previousStatus' => $this->previousStatus,
        ];
    }
}
