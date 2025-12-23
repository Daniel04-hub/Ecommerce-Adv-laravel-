<?php

namespace App\Events;

use App\Models\Order;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderStatusUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public Order $order,
        public string $previousStatus,
        public string $newStatus,
    ) {}

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): array
    {
        // Broadcast to customer's private channel
        $channels = [
            new PrivateChannel("orders.customer.{$this->order->user_id}"),
        ];

        // Also broadcast to vendor's private channel
        $channels[] = new PrivateChannel("orders.vendor.{$this->order->vendor_id}");

        return $channels;
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'id'              => $this->order->id,
            'product'         => $this->order->product?->name ?? 'Unknown Product',
            'quantity'        => $this->order->quantity,
            'price'           => number_format($this->order->price, 2),
            'status'          => $this->order->status,
            'previous_status' => $this->previousStatus,
            'updated_at'      => $this->order->updated_at->toIso8601String(),
        ];
    }

    /**
     * Get the broadcast event name.
     */
    public function broadcastAs(): string
    {
        return 'order.status-updated';
    }
}
