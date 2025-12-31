<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .container {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 40px 20px;
            border-radius: 10px;
            text-align: center;
            color: white;
        }
        .otp-box {
            background: white;
            color: #333;
            padding: 30px;
            margin: 20px 0;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .otp-code {
            font-size: 42px;
            font-weight: bold;
            letter-spacing: 8px;
            color: #667eea;
            margin: 20px 0;
            font-family: 'Courier New', monospace;
        }
        .warning {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
            color: #856404;
            text-align: left;
        }
        .footer {
            margin-top: 30px;
            font-size: 14px;
            color: rgba(255,255,255,0.8);
        }
        .purpose-badge {
            display: inline-block;
            background: rgba(255,255,255,0.2);
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 14px;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔐 OTP Verification</h1>
        
        @if($purpose === 'login')
            <div class="purpose-badge">Login Verification</div>
        @elseif($purpose === 'cod_verification')
            <div class="purpose-badge">COD Order Verification</div>
        @else
            <div class="purpose-badge">Account Verification</div>
        @endif
        
        <div class="otp-box">
            <p style="font-size: 16px; color: #666; margin-bottom: 10px;">Your One-Time Password is:</p>
            <div class="otp-code">{{ $code }}</div>
            <p style="font-size: 14px; color: #999; margin-top: 10px;">
                Valid for <strong>{{ $expiryMinutes }} minutes</strong>
            </p>
        </div>

        <div class="warning">
            <strong>⚠️ Security Notice:</strong>
            <ul style="margin: 10px 0; padding-left: 20px; text-align: left;">
                <li>Never share this OTP with anyone</li>
                <li>We will never ask for your OTP via phone or email</li>
                <li>This code expires in {{ $expiryMinutes }} minutes</li>
                <li>If you didn't request this, please ignore this email</li>
            </ul>
        </div>

        @if($purpose === 'login')
            <p style="color: white; margin-top: 20px;">
                Enter this code on the login page to access your account.
            </p>
        @elseif($purpose === 'cod_verification')
            <p style="color: white; margin-top: 20px;">
                Share this code with the delivery person to confirm your COD order.
            </p>
        @endif

        <div class="footer">
            <p>This is an automated message, please do not reply to this email.</p>
            <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
