<!doctype html>
<html>
<body style="margin:0;padding:0;background:#f5f7fa;font-family:Arial,Helvetica,sans-serif;">
	<table role="presentation" cellpadding="0" cellspacing="0" width="100%" style="background:#f5f7fa;padding:20px 0;">
		<tr>
			<td align="center">
				<table role="presentation" cellpadding="0" cellspacing="0" width="100%" style="max-width:600px;background:#ffffff;border:1px solid #e5e7eb;border-radius:8px;overflow:hidden;">
					<tr>
						<td style="background:#0f172a;color:#ffffff;padding:18px 24px;font-size:18px;font-weight:bold;">
							{{ config('app.name') }} | Order Confirmation
						</td>
					</tr>
					<tr>
						<td style="padding:24px;color:#0f172a;font-size:14px;line-height:1.6;">
							<p style="margin:0 0 12px 0;font-size:16px;font-weight:600;">Hello {{ $userName ?? 'Valued Customer' }},</p>
							<p style="margin:0 0 16px 0;">Thanks for shopping with us! We’ve received your order and it’s now being processed.</p>
							<table role="presentation" cellpadding="0" cellspacing="0" width="100%" style="margin:12px 0 16px 0;border-collapse:collapse;">
								<tr>
									<td style="padding:8px 0;width:140px;color:#6b7280;font-weight:600;">Order ID</td>
									<td style="padding:8px 0;color:#111827;font-weight:600;">{{ $orderId ?? 'N/A' }}</td>
								</tr>
								<tr>
									<td style="padding:8px 0;width:140px;color:#6b7280;font-weight:600;">Order Date</td>
									<td style="padding:8px 0;color:#111827;">{{ $orderDate ?? now()->toDateString() }}</td>
								</tr>
								<tr>
									<td style="padding:8px 0;width:140px;color:#6b7280;font-weight:600;">Order Total</td>
									<td style="padding:8px 0;color:#111827;font-size:16px;font-weight:700;">${{ isset($total) ? number_format($total, 2) : '0.00' }}</td>
								</tr>
							</table>
							<table role="presentation" cellpadding="0" cellspacing="0" style="margin:20px 0;">
								<tr>
									<td align="center" bgcolor="#2563eb" style="border-radius:6px;">
										<a href="{{ config('app.url') . '/orders/' . ($orderId ?? '#') }}" style="display:inline-block;padding:12px 24px;font-size:14px;color:#ffffff;text-decoration:none;font-weight:700;">View Order</a>
									</td>
								</tr>
							</table>
							<p style="margin:0 0 12px 0;color:#374151;">If you have any questions, just reply to this email—our support team is here to help.</p>
							<p style="margin:0;color:#6b7280;">Thank you for choosing {{ config('app.name') }}.</p>
						</td>
					</tr>
					<tr>
						<td style="background:#f9fafb;border-top:1px solid #e5e7eb;padding:16px 24px;color:#6b7280;font-size:12px;line-height:1.6;">
							Need help? Visit our Help Center or reply to this email.
						</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
</body>
</html>
