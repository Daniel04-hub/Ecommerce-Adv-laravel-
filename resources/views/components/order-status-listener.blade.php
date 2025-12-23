<!-- 
    Broadcasting Listener for Real-time Order Status Updates
    
    Usage:
    @include('components.order-status-listener', [
        'orderId' => $order->id,
        'userId' => auth()->id(),
        'isVendor' => auth()->user()->hasRole('vendor')
    ])
    
    This component:
    - Connects to Laravel Echo
    - Listens for order status changes
    - Shows toast notifications
    - Updates UI in real-time
-->

<script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.15.1/dist/echo.iife.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize Echo for broadcasting
        window.Echo = new Echo({
            broadcaster: 'reverb',
            key: @json(env('REVERB_APP_KEY')),
            wsHost: @json(env('REVERB_HOST')),
            wsPort: @json(env('REVERB_PORT')),
            wssPort: @json(env('REVERB_PORT')),
            forceTLS: false,
            enabledTransports: ['ws', 'wss'],
            auth: {
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            }
        });

        const userId = @json($userId ?? auth()->id());
        const isVendor = @json($isVendor ?? false);
        const orderId = @json($orderId ?? null);

        if (!userId || !orderId) return;

        // Customer listening to their orders
        if (!isVendor) {
            window.Echo.private(`orders.customer.${userId}`)
                .listen('OrderStatusUpdated', (event) => {
                    if (event.id === orderId) {
                        handleOrderStatusUpdate(event);
                    }
                });
        }

        // Vendor listening to their orders
        if (isVendor) {
            window.Echo.private(`orders.vendor.{{ auth()->user()->vendor_id ?? 'unknown' }}`)
                .listen('OrderStatusUpdated', (event) => {
                    if (event.id === orderId) {
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
                updateStatusBadge(statusEl, event.status);
            }

            // Update any status timeline/progress
            updateOrderTimeline(event.status);
        }

        function showNotification(event) {
            const messages = {
                'placed': '✅ Order Placed Successfully',
                'accepted': '📦 Order Accepted by Vendor',
                'shipped': '🚚 Order Shipped',
                'completed': '🎉 Order Delivered'
            };

            const message = messages[event.status] || `Order Status: ${event.status}`;
            
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
