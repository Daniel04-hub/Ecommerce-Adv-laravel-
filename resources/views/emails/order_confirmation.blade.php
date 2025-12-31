@component('mail::message')
# Order Confirmation

Hello {{ $userName }},

Thank you for your order! We're thrilled to have you as a customer.

**Order Details**
- **Order ID:** {{ $orderId }}
- **Total:** ${{ number_format($total, 2) }}

We're preparing your order for shipment and will send you a tracking number soon.

@component('mail::button', ['url' => config('app.url') . '/orders/' . $orderId])
View Your Order
@endcomponent

If you have any questions, please don't hesitate to contact our support team.

Thanks for shopping with us!

@endcomponent
