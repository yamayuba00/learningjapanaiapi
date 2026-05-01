<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Verification - {{ config('app.name') }}</title>
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
            background-color: #ecf0f1;
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
            color: #7f8c8d;
            font-size: 14px;
            margin-bottom: 10px;
        }
        .content {
            margin: 20px 0;
        }
        .warning {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            color: #856404;
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
        .button {
            display: inline-block;
            padding: 12px 30px;
            background-color: #3498db;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">{{ config('app.name') }}</div>
            <div class="subtitle">Japanese Learning Platform</div>
        </div>

        <div class="content">
            <h2>Hello {{ $user->name }}!</h2>
            
            <p>Thank you for registering with {{ config('app.name') }}. To complete your registration and verify your email address, please use the OTP code below:</p>

            <div class="otp-container">
                <div class="otp-label">Your Verification Code</div>
                <div class="otp-code">{{ $otp }}</div>
                <div style="color: #7f8c8d; font-size: 12px;">This code will expire in 10 minutes</div>
            </div>

            <p>Enter this code in the app to verify your email address and start your Japanese learning journey!</p>

            <div class="warning">
                <strong>Security Notice:</strong> If you didn't create an account with {{ config('app.name') }}, please ignore this email. Never share your verification code with anyone.
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