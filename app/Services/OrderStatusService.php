<?php

namespace App\Services;

use App\Models\Order;

class OrderStatusService
{
    /**
     * Update order status and broadcast the change
     *
     * @param Order $order
     * @param string $newStatus
     * @return bool
     */
    public static function update(Order $order, string $newStatus): bool
    {
        // Validate transition
        if (!$order->canTransitionTo($newStatus)) {
            return false;
        }

        // Update status only; observer will emit events as needed
        $order->update(['status' => $newStatus]);

        return true;
    }

    /**
     * Get readable status label with icon
     */
    public static function getStatusLabel(string $status): array
    {
        $labels = [
            'placed'    => ['label' => 'Order Placed', 'icon' => 'hourglass-split', 'color' => 'warning'],
            'accepted'  => ['label' => 'Accepted', 'icon' => 'check-circle', 'color' => 'info'],
            'shipped'   => ['label' => 'Shipped', 'icon' => 'truck', 'color' => 'primary'],
            'completed' => ['label' => 'Delivered', 'icon' => 'check-circle-fill', 'color' => 'success'],
        ];

        return $labels[$status] ?? ['label' => ucfirst($status), 'icon' => 'info-circle', 'color' => 'secondary'];
    }
}
