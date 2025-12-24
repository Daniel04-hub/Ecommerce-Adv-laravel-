<!doctype html>
<html>
<body style="margin:0;padding:0;background:#f5f7fa;font-family:Arial,Helvetica,sans-serif;">
	<table role="presentation" cellpadding="0" cellspacing="0" width="100%" style="background:#f5f7fa;padding:20px 0;">
		<tr>
			<td align="center">
				<table role="presentation" cellpadding="0" cellspacing="0" width="100%" style="max-width:600px;background:#ffffff;border:1px solid #e5e7eb;border-radius:8px;overflow:hidden;">
					<tr>
						<td style="background:#0f172a;color:#ffffff;padding:18px 24px;font-size:18px;font-weight:bold;">
							{{ config('app.name') }} | Shipping Update
						</td>
					</tr>
					<tr>
						<td style="padding:24px;color:#0f172a;font-size:14px;line-height:1.6;">
							<p style="margin:0 0 12px 0;font-size:16px;font-weight:600;">Hello {{ $userName ?? 'Valued Customer' }},</p>
							<p style="margin:0 0 16px 0;">Good news—your order is on the move! We’ve handed it over to the carrier.</p>
							<table role="presentation" cellpadding="0" cellspacing="0" width="100%" style="margin:12px 0 16px 0;border-collapse:collapse;">
								<tr>
									<td style="padding:8px 0;width:140px;color:#6b7280;font-weight:600;">Order ID</td>
									<td style="padding:8px 0;color:#111827;font-weight:600;">{{ $orderId ?? 'N/A' }}</td>
								</tr>
								<tr>
									<td style="padding:8px 0;width:140px;color:#6b7280;font-weight:600;">Shipping Status</td>
									<td style="padding:8px 0;color:#16a34a;font-weight:700;">{{ isset($status) ? ucfirst($status) : 'Shipped' }}</td>
								</tr>
								<tr>
									<td style="padding:8px 0;width:140px;color:#6b7280;font-weight:600;">Expected Delivery</td>
									<td style="padding:8px 0;color:#111827;">Estimated delivery in 3-5 business days.</td>
								</tr>
							</table>
							<table role="presentation" cellpadding="0" cellspacing="0" style="margin:20px 0;">
								<tr>
									<td align="center" bgcolor="#2563eb" style="border-radius:6px;">
										<a href="{{ config('app.url') . '/orders/' . ($orderId ?? '#') }}" style="display:inline-block;padding:12px 24px;font-size:14px;color:#ffffff;text-decoration:none;font-weight:700;">Track Order</a>
									</td>
								</tr>
							</table>
							<p style="margin:0 0 12px 0;color:#374151;">We’ll keep you posted as your package gets closer. Thanks for choosing {{ config('app.name') }}!</p>
							<p style="margin:0;color:#6b7280;">If you need help, reply to this email and we’ll assist you.</p>
						</td>
					</tr>
					<tr>
						<td style="background:#f9fafb;border-top:1px solid #e5e7eb;padding:16px 24px;color:#6b7280;font-size:12px;line-height:1.6;">
							You’re receiving this update because you placed an order with us.
						</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
</body>
</html>
