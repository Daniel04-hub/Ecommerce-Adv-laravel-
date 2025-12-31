<!doctype html>
<html>
<body style="margin:0;padding:0;background:#f5f7fa;font-family:Arial,Helvetica,sans-serif;">
	<table role="presentation" cellpadding="0" cellspacing="0" width="100%" style="background:#f5f7fa;padding:20px 0;">
		<tr>
			<td align="center">
				<table role="presentation" cellpadding="0" cellspacing="0" width="100%" style="max-width:600px;background:#ffffff;border:1px solid #e5e7eb;border-radius:8px;overflow:hidden;">
					<tr>
						<td style="background:#0f172a;color:#ffffff;padding:18px 24px;font-size:18px;font-weight:bold;">
							{{ config('app.name') }} | Security Alert
						</td>
					</tr>
					<tr>
						<td style="padding:24px;color:#0f172a;font-size:14px;line-height:1.6;">
							<p style="margin:0 0 12px 0;font-size:16px;font-weight:600;">Hello {{ $userName ?? 'Valued Customer' }},</p>
							<p style="margin:0 0 16px 0;">We detected a new sign-in to your account. Review the details below.</p>
							<table role="presentation" cellpadding="0" cellspacing="0" width="100%" style="margin:12px 0 16px 0;border-collapse:collapse;">
								<tr>
									<td style="padding:8px 0;width:160px;color:#6b7280;font-weight:600;">Login Time</td>
									<td style="padding:8px 0;color:#111827;">{{ $loginAt ?? now()->toDateTimeString() }}</td>
								</tr>
								<tr>
									<td style="padding:8px 0;width:160px;color:#6b7280;font-weight:600;">IP Address</td>
									<td style="padding:8px 0;color:#111827;">{{ $ipAddress ?? 'Unknown' }}</td>
								</tr>
								<tr>
									<td style="padding:8px 0;width:160px;color:#6b7280;font-weight:600;">Device / Browser</td>
									<td style="padding:8px 0;color:#111827;">{{ $device ?? 'Not Available' }}</td>
								</tr>
							</table>
							<p style="margin:0 0 12px 0;color:#b91c1c;font-weight:700;">If this wasn’t you, secure your account immediately.</p>
							<table role="presentation" cellpadding="0" cellspacing="0" style="margin:12px 0 20px 0;">
								<tr>
									<td align="center" bgcolor="#dc2626" style="border-radius:6px;">
										<a href="{{ config('app.url') . '/password/reset' }}" style="display:inline-block;padding:12px 24px;font-size:14px;color:#ffffff;text-decoration:none;font-weight:700;">Secure My Account</a>
									</td>
								</tr>
							</table>
							<p style="margin:0;color:#6b7280;">If you recognize this activity, no action is needed.</p>
						</td>
					</tr>
					<tr>
						<td style="background:#f9fafb;border-top:1px solid #e5e7eb;padding:16px 24px;color:#6b7280;font-size:12px;line-height:1.6;">
							This alert is sent for your account security. Keep your password safe and enable 2FA where possible.
						</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
</body>
</html>
