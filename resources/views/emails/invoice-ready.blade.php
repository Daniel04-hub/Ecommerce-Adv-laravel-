<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice Ready</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background: linear-gradient(135deg, #0d6efd 0%, #0b5ed7 100%);
            color: white;
            padding: 30px;
            text-align: center;
            border-radius: 8px 8px 0 0;
        }
        .content {
            background: #f8f9fa;
            padding: 30px;
            border-radius: 0 0 8px 8px;
        }
        .button {
            display: inline-block;
            padding: 12px 30px;
            background: #0d6efd;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
            font-weight: bold;
        }
        .info-box {
            background: white;
            padding: 15px;
            border-left: 4px solid #0d6efd;
            margin: 20px 0;
        }
        .warning {
            background: #fff3cd;
            border-left-color: #ffc107;
            padding: 15px;
            margin: 20px 0;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            color: #6c757d;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>📄 Your Invoice is Ready</h1>
    </div>

    <div class="content">
        <p>Hello <strong>{{ $order->full_name }}</strong>,</p>

        <p>Your invoice for Order <strong>#{{ str_pad($order->id, 8, '0', STR_PAD_LEFT) }}</strong> is now ready for download.</p>

        <div class="info-box">
            <strong>Order Details:</strong><br>
            Order ID: #{{ $order->id }}<br>
            Order Date: {{ $order->created_at->format('F d, Y') }}<br>
            Total Amount: ₹{{ number_format($order->price, 2) }}<br>
            Status: {{ ucfirst($order->status) }}
        </div>

        <p>Click the button below to download your invoice securely:</p>

        <div style="text-align: center;">
            <a href="{{ $invoiceUrl }}" class="button">Download Invoice</a>
        </div>

        <div class="warning">
            ⚠️ <strong>Important:</strong> This link will expire in <strong>{{ $expiresInHours }} hour{{ $expiresInHours > 1 ? 's' : '' }}</strong> for security reasons.
            If the link expires, please contact support or log into your account to generate a new one.
        </div>

        <p>Alternatively, you can always access your invoices by logging into your account:</p>
        
        <div style="text-align: center; margin: 20px 0;">
            <a href="{{ url('/customer/orders/' . $order->id) }}" style="color: #0d6efd; text-decoration: none;">
                View Order Details →
            </a>
        </div>

        <p>Thank you for your business!</p>
    </div>

    <div class="footer">
        <p>This is an automated email. Please do not reply to this message.</p>
        <p>&copy; {{ date('Y') }} E-Commerce Platform. All rights reserved.</p>
    </div>
</body>
</html>
