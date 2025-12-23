<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Login Alert</title>
</head>
<body>
    <h1>New Login Detected</h1>
    <p>Hello {{ $userName ?? 'Customer' }},</p>
    <p>We noticed a new login to your account.</p>
    @if(!empty($ipAddress))
        <p>IP address: {{ $ipAddress }}</p>
    @endif
    <p>If this wasn’t you, please secure your account immediately.</p>
</body>
</html>
