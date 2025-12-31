<p>Hello {{ $user?->name ?? 'Vendor' }},</p>

<p>Your vendor account <strong>{{ $vendor?->company_name ?? 'your company' }}</strong> has been approved.</p>

<p>You can now log in and start managing your products and orders.</p>

<p>Thanks,<br>{{ config('app.name') }}</p>
