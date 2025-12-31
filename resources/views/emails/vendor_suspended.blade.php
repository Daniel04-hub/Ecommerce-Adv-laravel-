<p>Hello {{ $user?->name ?? 'Vendor' }},</p>

<p>Your vendor account <strong>{{ $vendor?->company_name ?? 'your company' }}</strong> has been suspended.</p>

<p>If you believe this is a mistake, please contact support.</p>

<p>Thanks,<br>{{ config('app.name') }}</p>
