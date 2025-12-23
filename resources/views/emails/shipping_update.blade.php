<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Shipping Update</title>
</head>
<body>
    <h1>Your order is on the move</h1>
    <p>Hello {{ $userName ?? 'Customer' }},</p>
    <p>Your order #{{ $orderId }} is now {{ $status ?? 'in transit' }}.</p>
    <p>We will keep you posted with further updates.</p>
</body>
</html>
