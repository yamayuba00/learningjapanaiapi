<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Reset - {{ config('app.name') }}</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .container {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .logo {
            font-size: 28px;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 10px;
        }
        .subtitle {
            color: #7f8c8d;
            font-size: 16px;
        }
        .otp-container {
            background-color: #fff3cd;
            border: 2px solid #ffc107;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            margin: 30px 0;
        }
        .otp-code {
            font-size: 36px;
            font-weight: bold;
            color: #e74c3c;
            letter-spacing: 8px;
            margin: 10px 0;
            font-family: 'Courier New', monospace;
        }
        .otp-label {
            color: #856404;
            font-size: 14px;
            margin-bottom: 10px;
            font-weight: bold;
        }
        .content {
            margin: 20px 0;
        }
        .warning {
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .security-notice {
            background-color: #d1ecf1;
            border: 1px solid #bee5eb;
            color: #0c5460;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #ecf0f1;
            color: #7f8c8d;
            font-size: 14px;
        }
        .icon {
            font-size: 48px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="icon">🔐</div>
            <div class="logo">{{ config('app.name') }}</div>
            <div class="subtitle">Japanese Learning Platform</div>
        </div>

        <div class="content">
            <h2>Password Reset Request</h2>
            
            <p>Hello {{ $user->name }},</p>
            
            <p>We received a request to reset your password for your {{ config('app.name') }} account. To proceed with the password reset, please use the verification code below:</p>

            <div class="otp-container">
                <div class="otp-label">Password Reset Code</div>
                <div class="otp-code">{{ $otp }}</div>
                <div style="color: #856404; font-size: 12px;">This code will expire in 15 minutes</div>
            </div>

            <p>Enter this code in the app to verify your identity and set a new password.</p>

            <div class="security-notice">
                <strong>🛡️ Security Notice:</strong> 
                <ul style="margin: 10px 0; padding-left: 20px;">
                    <li>This code is only valid for 15 minutes</li>
                    <li>Never share this code with anyone</li>
                    <li>Our support team will never ask for this code</li>
                </ul>
            </div>

            <div class="warning">
                <strong>⚠️ Important:</strong> If you didn't request a password reset, please ignore this email and your password will remain unchanged. Consider changing your password if you suspect unauthorized access to your account.
            </div>
        </div>

        <div class="footer">
            <p>This is an automated message from {{ config('app.name') }}.<br>
            Please do not reply to this email.</p>
            
            <p>Need help? Contact us at <a href="mailto:support@japanai.com">support@japanai.com</a></p>
            
            <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
        </div>
    </div>
</body>
</html>