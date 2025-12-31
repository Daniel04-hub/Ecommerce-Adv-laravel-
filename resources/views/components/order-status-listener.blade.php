<!-- 
    Broadcasting Listener for Real-time Order Status Updates
-->

<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (!window.Echo) {
            console.warn('Echo not initialized');
            return;
        }

        @php
            $safeUserId = $userId ?? null;
            $safeOrderId = $orderId ?? null;
            $safeIsVendor = $isVendor ?? false;
            $safeVendorId = null;
            
            if ($safeIsVendor && auth()->check()) {
                try {
                    $user = auth()->user();
                    $safeVendorId = $user->vendor_id ?? null;
                } catch (\Exception $e) {
                    $safeVendorId = null;
                }
            }
        @endphp

        const userId = {{ $safeUserId ?? 'null' }};
        const isVendor = {{ $safeIsVendor ? 'true' : 'false' }};
        const orderId = {{ $safeOrderId ?? 'null' }};
        const vendorId = {{ $safeVendorId ?? 'null' }};

        if (!userId || !orderId) return;

        // Customer listening to their orders
        if (!isVendor) {
            window.Echo.private(`orders.customer.${userId}`)
                .listen('.order.status-updated', (event) => {
                    const id = event.id ?? event.orderId;
                    if (id === orderId) {
                        handleOrderStatusUpdate(event);
                    }
                });
        }

        // Vendor listening to their orders
        if (isVendor && vendorId) {
            window.Echo.private(`orders.vendor.${vendorId}`)
                .listen('.order.status-updated', (event) => {
                    const id = event.id ?? event.orderId;
                    if (id === orderId) {
                        handleOrderStatusUpdate(event);
                    }
                });
        }

        function handleOrderStatusUpdate(event) {
            console.log('Order status updated:', event);

            // Show toast notification
            showNotification(event);

            // Update order status in DOM
            const statusEl = document.querySelector(`[data-order-status="${orderId}"]`);
            if (statusEl) {
                const status = event.status ?? event.newStatus ?? 'updated';
                updateStatusBadge(statusEl, status);
            }

            // Update any status timeline/progress
            {
                const status = event.status ?? event.newStatus ?? 'updated';
                updateOrderTimeline(status);
            }
        }

        function showNotification(event) {
            const messages = {
                'placed': '✅ Order Placed Successfully',
                'accepted': '📦 Order Accepted by Vendor',
                'shipped': '🚚 Order Shipped',
                'completed': '🎉 Order Delivered'
            };

            const status = event.status ?? event.newStatus ?? 'updated';
            const message = messages[status] || `Order Status: ${status}`;
            
            // Bootstrap Toast
            const toastHtml = `
                <div class="toast align-items-center text-white bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
                    <div class="d-flex">
                        <div class="toast-body">
                            ${message}
                        </div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                    </div>
                </div>
            `;

            const toastContainer = document.getElementById('toast-container') || createToastContainer();
            toastContainer.insertAdjacentHTML('beforeend', toastHtml);

            const toastEl = toastContainer.querySelector('.toast:last-child');
            const toast = new bootstrap.Toast(toastEl);
            toast.show();

            // Auto-remove toast element after hiding
            toastEl.addEventListener('hidden.bs.toast', () => {
                toastEl.remove();
            });
        }

        function createToastContainer() {
            const container = document.createElement('div');
            container.id = 'toast-container';
            container.className = 'toast-container position-fixed bottom-0 end-0 p-3';
            document.body.appendChild(container);
            return container;
        }

        function updateStatusBadge(element, status) {
            const badges = {
                'placed': 'bg-warning',
                'accepted': 'bg-info',
                'shipped': 'bg-primary',
                'completed': 'bg-success'
            };

            element.className = 'badge ' + (badges[status] || 'bg-secondary');
            element.textContent = status.charAt(0).toUpperCase() + status.slice(1);

            // Add pulse animation
            element.style.animation = 'pulse 0.5s ease-in-out';
            setTimeout(() => {
                element.style.animation = 'none';
            }, 500);
        }

        function updateOrderTimeline(status) {
            const timeline = document.querySelector(`[data-timeline="${orderId}"]`);
            if (!timeline) return;

            // Update timeline visual indicators
            const steps = {
                'placed': 0,
                'accepted': 1,
                'shipped': 2,
                'completed': 3
            };

            const currentStep = steps[status];
            const stepElements = timeline.querySelectorAll('.timeline-step');

            stepElements.forEach((el, index) => {
                if (index <= currentStep) {
                    el.classList.add('completed');
                } else {
                    el.classList.remove('completed');
                }
            });
        }
    });
</script>

<style>
    @keyframes pulse {
        0%, 100% {
            opacity: 1;
        }
        50% {
            opacity: 0.7;
        }
    }
</style>
