<p>Hello {{ $user?->name ?? 'Vendor' }},</p>

<p>Your product <strong>{{ $product?->name ?? 'your product' }}</strong> was not approved and is currently inactive.</p>

<p>Thanks,<br>{{ config('app.name') }}</p>
