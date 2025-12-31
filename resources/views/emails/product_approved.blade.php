<p>Hello {{ $user?->name ?? 'Vendor' }},</p>

<p>Your product <strong>{{ $product?->name ?? 'your product' }}</strong> has been approved and is now active.</p>

<p>Thanks,<br>{{ config('app.name') }}</p>
